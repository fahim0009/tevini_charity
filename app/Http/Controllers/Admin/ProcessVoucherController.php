<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Imagick;
use \ImagickException;
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
    public function uploadBarcodePdf2(Request $request)
    {

        $data = $request->pdfFile;
        // $uniqueFileName = uniqid() . '_' . $request->file('pdfFile')->getClientOriginalName();
        // $path = $request->file('pdfFile')->storeAs('public/barcode/', $uniqueFileName);

        // $pdfPath = 'app/public/barcode/'.$uniqueFileName; // Your PDF file path
        $text = Pdf::getText($request->pdfFile);
        // Extract only the number part after "NO."
        // preg_match_all('/NO\.\s*([A-Za-z0-9]+)/', $text, $matches);
        preg_match_all('/NO\.\s*(\d+)/', $text, $matches);
        $numbers = $matches[1];

        // $parser = new Parser();
        // $pdf = $parser->parseFile($request->pdfFile);
        // $text = $pdf->getText();


        return response()->json(['status'=> 300,'data'=>$text]);
        
    }

    public function uploadBarcodePdf(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'pdfFile' => 'required|mimes:pdf|max:8048', // Ensure it's a PDF and max 2MB
        ]);

        // Store the uploaded PDF file temporarily
        $pdf = $request->file('pdfFile');
        $uniqueFileName = uniqid() . '_' . $pdf->getClientOriginalName();
        $pdfPath = $pdf->storeAs('pdfs', $uniqueFileName, 'public');

        // Get the absolute path of the file
        $fullPath = storage_path('app/public/' . $pdfPath);

        // return response()->json([
        //     'status' => 200,
        //     'numbers' => $fullPath,
        // ]);

        try {
            // Extract text from the PDF
            $text = Pdf::getText($fullPath);

            // Extract only integer numbers after "NO."
            // preg_match_all('/NO\.\s*(\d+)/', $text, $matches);

            // $numbers = $matches[1]; // Extracted integer values

            return response()->json([
                'status' => 200,
                'numbers' => $text,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function index()
    {


        $pdfPath = public_path('20250307.pdf'); // Adjust the path
        $binaryPath = 'D:\\poppler-24.08.0\\Library\\bin\\pdftotext.exe'; // Ensure this is correct

        $text = (new Pdf($binaryPath))->setPdf($pdfPath)->text();
    
        // $text = Pdf::getText(public_path('sample-demo.pdf'));
        dd($text);

    }

    public function convertPdfToText_old(Request $request)
    {
        
        // Validate the uploaded file
        $request->validate([
            'pdfFile' => 'required|mimes:pdf|max:8048',
        ]);

        // Get the uploaded file
        $pdfFile = $request->file('pdfFile');

        // dd($pdfFile);
        // Initialize the PDF parser
        $parser = new Parser();

        try {
            // If parsing fails, assume it's an image-based PDF and use OCR
            $text = $this->ocrPdfToText($pdfFile->getPathname());

            // Replace common misinterpretations
            $replacements = [
                'D' => '0', // Replace D with 0
                'I' => '1', // Replace I with 1
                'm' => '1', // Replace m with 1
                'n' => '0', // Replace n with 0
                's' => '5', // Replace s with 5
                'G' => '6', // Replace G with 6
                'B' => '8', // Replace B with 8
            ];

            dd($replacements);

            $cleanText = str_replace(array_keys($replacements), array_values($replacements), $text);

            // preg_match_all('/NO\.\s*(\d+)/', $text, $matches);
            preg_match_all('/NO\.\s*([A-Za-z0-9]+)/', $text, $cleanText);
            $numbers = $cleanText[1];

            



        } catch (\Exception $e) {
            // Try to parse the PDF as text-based
            $pdf = $parser->parseFile($pdfFile->getPathname());
            $text = $pdf->getText();

            // Replace common misinterpretations
            $replacements = [
                'mD' => '010', // Replace mD with 010
                'D' => '0', // Replace D with 0
                'I' => '1', // Replace I with 1
                'i' => '1', // Replace i with 1
                'm' => '1', // Replace m with 1
                'n' => '0', // Replace n with 0
                's' => '5', // Replace s with 5
                'G' => '6', // Replace G with 6
                'B' => '8', // Replace B with 8
            ];
            $cleanText = str_replace(array_keys($replacements), array_values($replacements), $text);
            
            
            // preg_match_all('/NO\.\s*(\d+)/', $cleanText, $matches);
            preg_match_all('/NO\.\s*([A-Za-z0-9]+)/', $cleanText, $matches);
            $numbers = $matches[1];
            dd($numbers);
        }

        // Save the text to a file
        $textFileName = 'converted_text_' . time() . '.txt';
        file_put_contents(storage_path('app/public/' . $textFileName), $numbers);

        // Return the text file for download
        return response()->download(storage_path('app/public/' . $textFileName))->deleteFileAfterSend(true);
    }

    private function ocrPdfToText($pdfPath)
    {
        // Convert PDF to images
        $pdf = new Pdf($pdfPath);
        $outputDir = storage_path('app/public/pdf_images');
        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        $text = '';
        $pageCount = $this->getPdfPageCount($pdfPath);

        for ($pageNumber = 1; $pageNumber <= $pageCount; $pageNumber++) {
            $imagePath = $outputDir . '/page_' . $pageNumber . '.jpg';
            $pdf->setPage($pageNumber)->saveImage($imagePath);

            // Perform OCR on the image
            $ocrText = (new TesseractOCR($imagePath))->lang('eng')->whitelist('0123456789')->run();
            $text .= $ocrText . "\n";

            // Clean up the image file
            unlink($imagePath);
        }

        return $text;
    }

    private function getPdfPageCount($pdfPath)
    {
        // Use smalot/pdfparser to get the number of pages
        $parser = new Parser();
        $pdf = $parser->parseFile($pdfPath);
        $details = $pdf->getDetails();
        return $details['Pages'] ?? 1;
    }

    function extractImagesFromPdf($pdfPath) {
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($pdfPath);
        $images = [];
    
        foreach ($pdf->getObjectsByType('XObject', 'Image') as $image) {
            try {
                // Ensure the image has valid bits per component
                if (!$image->get('BitsPerComponent')) {
                    continue;
                }
    
                $imageContent = $image->getContent();
                if (!$imageContent) {
                    \Log::warning('Empty image content found.');
                    continue;
                }
    
                $imagick = new \Imagick();
                $imagick->readImageBlob($imageContent);
    
                // Convert color space if needed
                if ($imagick->getImageColorspace() == \Imagick::COLORSPACE_CMYK) {
                    $imagick->setImageColorspace(\Imagick::COLORSPACE_RGB);
                }
    
                // Convert to PNG format
                $imagick->setImageFormat('png');
                $imagick->setImageDepth(8);
                $imagick->stripImage(); // Remove extra metadata
    
                $images[] = $imagick->getImageBlob();
                // dd($images);
            } catch (\ImagickException $e) {
                \Log::error('Imagick error: ' . $e->getMessage());
                continue;
            } catch (\Exception $e) {
                \Log::error('General error: ' . $e->getMessage());
                continue;
            }
        }
    
        return $images;
    }
    

    function decodeBarcodeFromImage($imageContent) {
        // Save the image content to a temporary file
        $tempImagePath = tempnam(sys_get_temp_dir(), 'barcode') . '.png';
        file_put_contents($tempImagePath, $imageContent);
    
        // Decode the barcode
        $qrReader = new QrReader($tempImagePath);
        $barcodeText = $qrReader->text();
    
        // Clean up the temporary file
        unlink($tempImagePath);
    
        return $barcodeText;
    }

    public function convertPdfToText(Request $request) {
        
        // Validate the uploaded file
        $request->validate([
            'pdfFile' => 'required|mimes:pdf',
        ]);
    
        // Save the uploaded file temporarily
        $pdfPath = $request->file('pdfFile')->getRealPath();

        // Extract images from the PDF
        $images = $this->extractImagesFromPdf($pdfPath);

        // Decode barcodes from the extracted images
        $barcodes = [];
        foreach ($images as $image) {
            // dd($image);
            $barcodeText = $this->decodeBarcodeFromImage($image);
            if ($barcodeText) {
                $barcodes[] = $barcodeText;
            }
        }

        dd($barcodes);
    
        // Return the decoded barcodes
        return response()->json(['barcodes' => $barcodes]);
    }

    public function uploadBarcode2(Request $request)
    {
        $request->validate([
            'barcode_image' => 'required|image|mimes:jpeg,png,jpg|max:8048'
        ]);

        // dd($request->all());

        // Store image in Laravel's storage (public folder)
        $imagePath = $request->file('barcode_image')->store('barcodes', 'public');
        $fullImagePath = Storage::path("public/$imagePath");

        // ✅ Use GD Library to convert image to PNG (if needed)
        $image = imagecreatefromstring(file_get_contents($fullImagePath));
        if (!$image) {
            return back()->with('error', 'Failed to process image.');
        }

        // Save as PNG (for better barcode detection)
        $newFilePath = Storage::path("public/barcodes/barcode.png");
        imagepng($image, $newFilePath);
        imagedestroy($image); // Free up memory

        // ✅ Use ZBar to scan barcode
        $barcodeOutput = shell_exec("zbarimg " . escapeshellarg($newFilePath) . " 2>&1");

        dd($barcodeOutput);
        // ✅ Extract barcode number from output
        preg_match('/(?<=CODE-128:)[0-9]+/', $barcodeOutput, $matches);
        $barcodeNumber = $matches[0] ?? null;

        if (!$barcodeNumber) {
            return back()->with('error', 'No barcode detected. Try another image.');
        }

        return back()->with('success', "Barcode Number: $barcodeNumber");
    }

    public function scanBarcode2(Request $request)
    {
        $request->validate(['barcode_image' => 'required|image']);

        $imagePath = $request->file('barcode_image')->store('barcodes', 'public');
        $fullImagePath = storage_path("app/public/$imagePath");

        $qrcode = new QrReader($fullImagePath);
        dd($qrcode);
        $text = $qrcode->text(); // Get barcode text


        return response()->json(['barcode' => $text]);
    }

    public function scanBarcode(Request $request)
    {
        $request->validate(['barcode_image' => 'required|image']);

        $imagePath = $request->file('barcode_image')->store('barcodes', 'public');
        $fullImagePath = storage_path("app/public/$imagePath");

        // Path to ZXing JAR
        $zxingJarPath = storage_path('app/zxing.jar');

        // Run ZXing
        $command = "java -jar " . escapeshellarg($zxingJarPath) . " --try_harder " . escapeshellarg($fullImagePath);
        $barcodeOutput = shell_exec($command);
        dd($barcodeOutput);

        return response()->json(['barcode' => trim($barcodeOutput)]);
    }

    public function upload2(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'pdfFile' => 'required|mimes:pdf|max:8048',
        ]);

        // Get the uploaded file
        $pdfFile = $request->file('pdfFile');

        // Parse the PDF
        $parser = new Parser();
        $pdf = $parser->parseFile($pdfFile->getPathname());

        // Extract images from the PDF
        $images = [];
        foreach ($pdf->getObjectsByType('XObject', 'Image') as $image) {
            $images[] = $image->getContent();
        }

        // Decode barcodes from the images
        $barcodeNumbers = [];
        $reader = new BarcodeReader();

        dd($reader);
        foreach ($images as $image) {
            $tempImagePath = tempnam(sys_get_temp_dir(), 'barcode');
            file_put_contents($tempImagePath, $image);

            $barcodeNumbers[] = $reader->decode($tempImagePath);

            unlink($tempImagePath); // Clean up the temporary file
        }

        // Filter out null values and flatten the array
        $barcodeNumbers = array_filter($barcodeNumbers);
        $barcodeNumbers = array_map(function ($code) {
            return substr($code, 0, 7); // Extract the first 6/7 digits
        }, $barcodeNumbers);

        // Dump and die the barcode numbers
        dd($barcodeNumbers);
    }

    public function upload(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'pdfFile' => 'required|mimes:pdf|max:8048',
        ]);

        // Get the uploaded file
        $pdfFile = $request->file('pdfFile');

        // Parse the PDF
        $parser = new Parser();
        $pdf = $parser->parseFile($pdfFile->getPathname());

        // Extract images from the PDF
        $images = [];
        foreach ($pdf->getObjectsByType('XObject', 'Image') as $image) {
            $images[] = $image->getContent();
        }


        // Decode barcodes from the images
        $barcodeNumbers = [];

        foreach ($images as $index => $image) {

            $tempImagePath = storage_path("app/barcode_image_$index.jpg");
            file_put_contents($tempImagePath, $image);
            // echo "Saved image to: $tempImagePath\n";
            // $tempImagePath = tempnam(sys_get_temp_dir(), 'barcode');
            // file_put_contents($tempImagePath, $image);

            // Use QrReader to decode the barcode
            $qrReader = new QrReader($tempImagePath);
            $decodedText = $qrReader->text();

            dd($decodedText );
            if ($decodedText) {
                // Extract the first 6/7 digits
                $barcodeNumbers[] = substr($decodedText, 0, 7);
            }

            unlink($tempImagePath); // Clean up the temporary file
        }

        // Dump and die the barcode numbers
        dd($barcodeNumbers);
    }

    public function uploadAndExtract2(Request $request)
    {
        // Validate Image Upload
        $request->validate([
            'barcode_image' => 'required|mimes:jpg,jpeg,png|max:8048'
        ]);

        // Store Image
        $path = $request->file('barcode_image')->store('public/vouchers');
        $imagePath = storage_path('app/' . $path);
        // Manually set the Tesseract path
        $tesseractPath = "C:\\Program Files\\Tesseract-OCR\\tesseract.exe";
        // Extract Text using Tesseract OCR
        $text = (new TesseractOCR($imagePath))
                        ->executable($tesseractPath) // Set the path
                        ->lang('eng')->run();

        // Extract Voucher Number (Assuming Format: NO. XXXXXXX)
        preg_match('/NO\.\s*(\d{6,})/', $text, $matches);

        dd($matches[1]);
        if (!empty($matches[1])) {
            return response()->json(['voucher_number' => $matches[1]]);
        } else {
            return response()->json(['message' => 'Voucher number not found'], 404);
        }
    }

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
    public function uploadAndExtract(Request $request)
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



}
