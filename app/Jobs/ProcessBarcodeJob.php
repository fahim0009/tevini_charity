<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\PdfToImage\Pdf;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Illuminate\Support\Facades\DB;
use Imagick;

class ProcessBarcodeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $pdfFullPath;
    protected $barcodeImagePath;
    protected $tesseractPath;

    

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($pdfFullPath, $barcodeImagePath, $tesseractPath)
    {
        $this->pdfFullPath = $pdfFullPath;
        $this->barcodeImagePath = $barcodeImagePath;
        $this->tesseractPath = $tesseractPath;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $pdf = new Pdf($this->pdfFullPath);
            $numberOfPages = $pdf->getNumberOfPages();
            $images = [];

            for ($i = 1; $i <= $numberOfPages; $i++) {
                $imagePath = $this->barcodeImagePath . "page_{$i}_" . time() . ".jpg";
                $pdf->setPage($i)->saveImage($imagePath);
                $images[] = $imagePath;
            }

            $barcodes = [];

            foreach ($images as $imagePath) {
                if (!$this->isValidImage($imagePath)) {
                    $barcodes[] = [
                        'file' => basename($imagePath),
                        'voucher_number' => 'Image unreadable'
                    ];
                    DB::table('processed_barcodes')->insert([
                        'file' => $barcodes['file'],
                        'barcode' => $barcodes['voucher_number'],
                        'status' => 'Unreadable',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    continue;
                }

                try {
                    $text = (new TesseractOCR($imagePath))
                                ->executable($this->tesseractPath)
                                ->lang('eng')
                                ->psm(6)
                                ->oem(1)
                                ->run();
                } catch (\Exception $e) {
                    continue;
                }

                preg_match('/NO\.\s*(\d{6,})/', $text, $matches);

                $barcodes[] = [
                    'file' => basename($imagePath),
                    'voucher_number' => !empty($matches[1]) ? $matches[1] : 'Not Found'
                ];
            }

            // Process voucher status
            $this->processVoucher($barcodes);
        } catch (\Exception $e) {
            \Log::error('Barcode processing failed: ' . $e->getMessage());
        }
    }

    private function isValidImage($imagePath)
    {
        $image = new Imagick($imagePath);
        if ($image->getImageWidth() < 100 || $image->getImageHeight() < 100) {
            return false;
        }
        if ($image->getImageLength() < 5000) {
            return false;
        }
        return true;
    }


    private function processVoucher($barcodes)
    {
        $voucherNumbers = array_unique(array_column($barcodes, 'voucher_number'));
        $existingVouchers = DB::table('barcodes')
                            ->whereIn('barcode', $voucherNumbers)
                            ->pluck('barcode')
                            ->toArray();

        foreach ($barcodes as $barcode) {
            DB::table('processed_barcodes')->insert([
                'file' => $barcode['file'],
                'barcode' => $barcode['voucher_number'],
                'status' => in_array($barcode['voucher_number'], $existingVouchers) ? 'Found' : 'Not Found',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
