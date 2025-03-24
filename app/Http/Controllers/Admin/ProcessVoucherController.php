<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Imagick;
use ImagickException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
// use Spatie\PdfToText\Pdf;
use Smalot\PdfParser\Parser;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Spatie\PdfToImage\Pdf;

use Zxing\QrReader;

use Illuminate\Support\Facades\Storage;
use Lukeraymonddowning\BarcodeScanner\Scanner;

use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeReader;

class ProcessVoucherController extends Controller
{
    

    // its working 
    public function uploadAndExtractMultiple(Request $request)
    {
        
        // Validate multiple image uploads
        $request->validate([
            'barcode_image.*' => 'required|mimes:jpg,jpeg,png|max:8048'
        ]);
        $results = [];
        // $imageCount = count($request->file('barcode_image'));
        // dd($imageCount);
        // dd($request->all());
        foreach ($request->file('barcode_image') as $image) {
            // Store each image
            $path = $image->store('public/vouchers');
            
            $imagePath = storage_path('app/' . $path);

            // Extract text using Tesseract OCR
            $tesseractPath = "C:\\Program Files\\Tesseract-OCR\\tesseract.exe"; // Set Tesseract path
            $text = (new TesseractOCR($imagePath))
                        ->executable($tesseractPath)
                        ->lang('eng')
                        ->run();

            // Extract Voucher Number (Assuming Format: NO. XXXXXXX)
            preg_match('/NO\.\s*(\d{6,})/', $text, $matches);
            // dd($imagePath);
            if (!empty($matches[1])) {
                $results[] = [
                    'file' => $image->getClientOriginalName(),
                    'voucher_number' => $matches[1]
                ];
            } else {
                $results[] = [
                    'file' => $image->getClientOriginalName(),
                    'voucher_number' => 'Not Found'
                ];
            }
        }

        dd($results);

        // return response()->json($results);
    }


    public function uploadAndExtract12(Request $request)
    {

        // ✅ Set Ghostscript path manually
        putenv("MAGICK_HOME=C:\\Program Files\\gs\\gs10.05.0\\bin");
        putenv("PATH=" . getenv("MAGICK_HOME") . ";" . getenv("PATH"));
        putenv("GS_PROG=C:\\Program Files\\gs\\gs10.05.0\\bin\\gswin64c.exe"); // Ghostscript path


        // ✅ Validate PDF Upload
        $request->validate([
            'pdfFile' => 'required|mimes:pdf|max:10000'
        ]);

        // ✅ Store PDF File
        $pdfPath = $request->file('pdfFile')->store('public/pdfs');
        $pdfFullPath = storage_path('app/' . $pdfPath);

        
        // ✅ Convert PDF to Images
        $pdf = new Pdf($pdfFullPath);
        $numberOfPages = $pdf->getNumberOfPages();

        // dd($numberOfPages);

        $images = [];
        for ($i = 1; $i <= $numberOfPages; $i++) {
            $imagePath = storage_path("app/public/barcodeimages/page_{$i}_" . now()->timestamp . ".jpg");
            $pdf->setPage($i)->saveImage($imagePath);
            $images[] = $imagePath;
        }

        // ✅ Extract Barcodes from Images
        $barcodes = [];

        foreach ($request->file('barcode_image') as $image) {
            // Store each image
            $path = $image->store('public/vouchers');
            
            $imagePath = storage_path('app/' . $path);

            // Extract text using Tesseract OCR
            $tesseractPath = "C:\\Program Files\\Tesseract-OCR\\tesseract.exe"; // Set Tesseract path
            $text = (new TesseractOCR($imagePath))
                        ->executable($tesseractPath)
                        ->lang('eng')
                        ->run();

            // Extract Voucher Number (Assuming Format: NO. XXXXXXX)
            preg_match('/NO\.\s*(\d{6,})/', $text, $matches);
            // dd($imagePath);
            if (!empty($matches[1])) {
                $results[] = [
                    'file' => $image->getClientOriginalName(),
                    'voucher_number' => $matches[1]
                ];
            } else {
                $results[] = [
                    'file' => $image->getClientOriginalName(),
                    'voucher_number' => 'Not Found'
                ];
            }
        }

        dd($barcodes);
        return response()->json($barcodes);
    }

    // working..
    public function uploadAndExtract112(Request $request)
    {
        // ✅ Set Ghostscript path manually
        putenv("MAGICK_HOME=C:\\Program Files\\gs\\gs10.05.0\\bin");
        putenv("PATH=" . getenv("MAGICK_HOME") . ";" . getenv("PATH"));
        putenv("GS_PROG=C:\\Program Files\\gs\\gs10.05.0\\bin\\gswin64c.exe"); // Ghostscript path

        // ✅ Validate PDF Upload
        $request->validate([
            'pdfFile' => 'required|mimes:pdf|max:40000'
        ]);

        // ✅ Store PDF File
        $pdfPath = $request->file('pdfFile')->store('public/pdfs');
        $pdfFullPath = storage_path('app/' . $pdfPath);

        // ✅ Ensure barcode images directory exists
        $barcodeImagePath = storage_path('app/public/barcodeimages/');
        if (!file_exists($barcodeImagePath)) {
            mkdir($barcodeImagePath, 0777, true);
        }

        // ✅ Convert PDF to Images
        $pdf = new Pdf($pdfFullPath);
        $numberOfPages = $pdf->getNumberOfPages();

        $images = [];
        for ($i = 1; $i <= $numberOfPages; $i++) {
            $imagePath = $barcodeImagePath . "page_{$i}_" . time() . ".jpg";
            $pdf->setPage($i)->saveImage($imagePath);
            $images[] = $imagePath;
        }

        // ✅ Extract Barcodes from Generated Images
        $barcodes = [];
        $tesseractPath = "C:\\Program Files\\Tesseract-OCR\\tesseract.exe"; // Set Tesseract path

        foreach ($images as $imagePath) {
            // Extract text using Tesseract OCR
            $text = (new TesseractOCR($imagePath))
                        ->executable($tesseractPath)
                        ->lang('eng')
                        ->run();

            // Extract Voucher Number (Assuming Format: NO. XXXXXXX)
            preg_match('/NO\.\s*(\d{6,})/', $text, $matches);

            if (!empty($matches[1])) {
                $barcodes[] = [
                    'file' => basename($imagePath),
                    'voucher_number' => $matches[1]
                ];
            } else {
                $barcodes[] = [
                    'file' => basename($imagePath),
                    'voucher_number' => 'Not Found'
                ];
            }
        }
        dd($barcodes);

        return response()->json($barcodes);
    }

    public function uploadAndExtract(Request $request)
    {
        try {
            // ✅ Set Ghostscript and Tesseract paths manually
            putenv("MAGICK_HOME=C:\\Program Files\\gs\\gs10.05.0\\bin");
            putenv("PATH=" . getenv("MAGICK_HOME") . ";" . getenv("PATH"));
            putenv("GS_PROG=C:\\Program Files\\gs\\gs10.05.0\\bin\\gswin64c.exe");
            $tesseractPath = "C:\\Program Files\\Tesseract-OCR\\tesseract.exe"; 

            // ✅ Validate PDF Upload
            $request->validate([
                'pdfFile' => 'required|mimes:pdf|max:40000'
            ]);


            // ✅ Store PDF File
            $pdfPath = $request->file('pdfFile')->store('public/pdfs');
            $pdfFullPath = storage_path('app/' . $pdfPath);

            // ✅ Ensure barcode images directory exists
            $barcodeImagePath = storage_path('app/public/barcodeimages/');
            if (!file_exists($barcodeImagePath)) {
                mkdir($barcodeImagePath, 0777, true);
            }

            // ✅ Convert PDF to Images
            $pdf = new Pdf($pdfFullPath);
            $numberOfPages = $pdf->getNumberOfPages();
            $images = [];

            for ($i = 1; $i <= $numberOfPages; $i++) {
                $imagePath = $barcodeImagePath . "page_{$i}_" . time() . ".jpg";
                $pdf->setPage($i)->saveImage($imagePath);
                $this->preprocessImage($imagePath); // ✅ Apply preprocessing before OCR
                $images[] = $imagePath;
            }

            // ✅ Extract Barcodes from Generated Images
            $barcodes = [];

            foreach ($images as $imagePath) {
                // ✅ Validate the image before OCR
                if (!$this->isValidImage($imagePath)) {
                    $barcodes[] = [
                        'file' => basename($imagePath),
                        'voucher_number' => 'Image unreadable'
                    ];
                    continue;
                }

                // ✅ Extract text using Tesseract OCR
                try {
                    $text = (new TesseractOCR($imagePath))
                                ->executable($tesseractPath)
                                ->lang('eng')
                                ->psm(6)
                                ->oem(1)
                                ->run();
                } catch (\Exception $e) {
                    continue; // Skip this image if Tesseract fails
                }

                // ✅ Extract Voucher Number (Assuming Format: NO. XXXXXXX)
                preg_match('/NO\.\s*(\d{6,})/', $text, $matches);

                $barcodes[] = [
                    'file' => basename($imagePath),
                    'voucher_number' => !empty($matches[1]) ? $matches[1] : 'Not Found'
                ];
            }
            
            $processVoucher = $this->processVoucher($barcodes);

            return response()->json([
                'message' => 'Barcode extraction successful',
                'barcodes' => $barcodes,
                'processVoucher' => $processVoucher
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    private function preprocessImage($imagePath) {
        $image = new Imagick($imagePath);
        $image->setImageType(Imagick::IMGTYPE_GRAYSCALE);
        $image->thresholdImage(0.5 * Imagick::getQuantumRange()['quantumRangeLong']);
        $image->writeImage($imagePath);
    }

    private function preprocessImage2($imagePath)
    {
        $img = new Imagick($imagePath);
        $img->setImageType(Imagick::IMGTYPE_GRAYSCALE); // Convert to grayscale
        $img->contrastImage(3); // Increase contrast for better visibility
        $img->normalizeImage(); // Normalize image for uniform intensity
        $img->thresholdImage(0.5 * Imagick::getQuantumRange()['quantumRangeLong']); // Apply thresholding
        $img->writeImage($imagePath);
        $img->clear();
        $img->destroy();
    }
    

    private function isValidImage($imagePath)
    {
        $image = new Imagick($imagePath);
        
        // ✅ Skip if image is too small (likely blank)
        if ($image->getImageWidth() < 100 || $image->getImageHeight() < 100) {
            return false;
        }

        // ✅ Skip if image has no data (possibly empty)
        if ($image->getImageLength() < 5000) { // Less than 5KB? Likely empty.
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

        return array_map(function ($barcode) use ($existingVouchers) {
            return [
                'file' => $barcode['file'],
                'barcodes' => $barcode['voucher_number'],
                'status' => in_array($barcode['voucher_number'], $existingVouchers) ? 'Found' : 'Not Found'
            ];
        }, $barcodes);
    }








}
