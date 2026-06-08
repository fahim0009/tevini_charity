<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Picqer\Barcode\BarcodeReader;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessBarcodeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $pdfFullPath;
    protected $barcodeImagePath;
    protected $tesseractPath;
    protected $ghostscriptPath;

    public function __construct($pdfFullPath, $barcodeImagePath, $tesseractPath, $ghostscriptPath)
    {
        $this->pdfFullPath = $pdfFullPath;
        $this->barcodeImagePath = $barcodeImagePath;
        $this->tesseractPath = $tesseractPath;
        $this->ghostscriptPath = $ghostscriptPath;
    }

    public function handle()
    {
        try {
            Log::info('Job started: ' . $this->pdfFullPath);

            // Convert PDF to images
            $images = $this->convertPdfToImages();
            Log::info('Converted to ' . count($images) . ' images');

            // Extract barcodes (OCR + Barcode fallback)
            $barcodes = $this->extractBarcodesFromImages($images);

            // Store results
            $this->processVoucher($barcodes);

            Log::info('Job completed successfully');

        } catch (\Exception $e) {
            Log::error('Job failed: ' . $e->getMessage());
        }
    }

    private function convertPdfToImages()
    {
        $prefix = 'job_' . time() . '_';
        $outputPath = $this->barcodeImagePath . $prefix . '%d.jpg';

        $command = $this->ghostscriptPath . 
            ' -dNOPAUSE -dBATCH -dSAFER -sDEVICE=jpeg' .
            ' -dTextAlphaBits=4 -dGraphicsAlphaBits=4' .
            ' -r200 -dJPEGQ=90' .
            ' -sOutputFile="' . $outputPath . '"' .
            ' "' . $this->pdfFullPath . '"';

        exec($command . ' 2>&1', $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \Exception('Ghostscript failed: ' . implode("\n", $output));
        }

        $images = glob($this->barcodeImagePath . $prefix . '*.jpg');
        sort($images);

        return $images;
    }

    private function extractBarcodesFromImages($images)
    {
        $barcodes = [];
        $total = count($images);

        foreach ($images as $index => $imagePath) {
            Log::info("Processing {$index}/{$total}");

            if (!$this->isValidImage($imagePath)) {
                $barcodes[] = [
                    'file' => basename($imagePath),
                    'voucher_number' => 'Image unreadable',
                    'method' => 'none'
                ];
                continue;
            }

            $voucherNumber = null;
            $method = 'none';

            // Method 1: OCR
            $voucherNumber = $this->extractByOCR($imagePath);
            if ($voucherNumber) $method = 'OCR';

            // Method 2: Barcode
            if (!$voucherNumber) {
                $voucherNumber = $this->extractByBarcode($imagePath);
                if ($voucherNumber) $method = 'Barcode';
            }

            // Method 3: Enhanced OCR
            if (!$voucherNumber) {
                $enhanced = $this->enhanceImage($imagePath);
                if ($enhanced) {
                    $voucherNumber = $this->extractByOCR($enhanced);
                    if ($voucherNumber) $method = 'OCR Enhanced';
                    @unlink($enhanced);
                }
            }

            $barcodes[] = [
                'file' => basename($imagePath),
                'voucher_number' => $voucherNumber ?? 'Not Found',
                'method' => $method
            ];
        }

        return $barcodes;
    }

    private function extractByOCR($imagePath)
    {
        try {
            $text = (new TesseractOCR($imagePath))
                ->executable($this->tesseractPath)
                ->lang('eng')
                ->psm(6)
                ->oem(1)
                ->run();

            if (preg_match('/NO\.\s*(\d{6,})/i', $text, $matches)) return $matches[1];
            if (preg_match('/Cheque\s*No\.?\s*[:\-]?\s*(\d{6,})/i', $text, $matches)) return $matches[1];
            if (preg_match('/Check\s*No\.?\s*[:\-]?\s*(\d{6,})/i', $text, $matches)) return $matches[1];
            if (preg_match('/\b(\d{6,12})\b/', $text, $matches)) return $matches[1];

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function extractByBarcode($imagePath)
    {
        try {
            $reader = new BarcodeReader();
            $reader->setFile($imagePath);
            $result = $reader->read();

            if ($result && !empty($result->text)) {
                $value = $result->text;
                if (preg_match('/^\d{6,12}$/', $value)) return $value;
                if (preg_match('/(\d{6,12})/', $value, $matches)) return $matches[1];
            }
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function enhanceImage($imagePath)
    {
        try {
            $enhanced = str_replace('.jpg', '_enh.jpg', $imagePath);
            exec('magick "' . $imagePath . '" -colorspace GRAY -threshold 50% -negate "' . $enhanced . '" 2>&1', $out, $code);
            return ($code === 0 && file_exists($enhanced)) ? $enhanced : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function isValidImage($imagePath)
    {
        if (!file_exists($imagePath)) return false;
        $info = @getimagesize($imagePath);
        if (!$info) return false;
        if ($info[0] < 100 || $info[1] < 100) return false;
        if (filesize($imagePath) < 5000) return false;
        return true;
    }

    private function processVoucher($barcodes)
    {
        $voucherNumbers = array_filter(
            array_unique(array_column($barcodes, 'voucher_number')),
            fn($v) => $v !== 'Not Found' && $v !== 'Image unreadable'
        );

        $existing = DB::table('barcodes')
            ->whereIn('barcode', $voucherNumbers)
            ->pluck('barcode')
            ->toArray();

        foreach ($barcodes as $barcode) {
            $status = 'Not Found';
            if ($barcode['voucher_number'] === 'Image unreadable') $status = 'Unreadable';
            elseif (in_array($barcode['voucher_number'], $existing)) $status = 'Found';

            DB::table('processed_barcodes')->insert([
                'file' => $barcode['file'],
                'barcode' => $barcode['voucher_number'],
                'status' => $status,
                'method' => $barcode['method'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}