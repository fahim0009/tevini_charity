<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Voucher;
use App\Models\Charity;
use App\Models\Provoucher;
use App\Models\User;
use App\Models\Order;
use App\Models\Barcode;
use App\Models\OrderHistory;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
// use Spatie\PdfToText\Pdf;
use Smalot\PdfParser\Parser;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Spatie\PdfToImage\Pdf;

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

    public function convertPdfToText(Request $request)
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

            
            // preg_match_all('/NO\.\s*(\d+)/', $text, $matches);
            preg_match_all('/NO\.\s*([A-Za-z0-9]+)/', $text, $matches);
            $numbers = $matches[1];
            dd($numbers);

        } catch (\Exception $e) {
            // Try to parse the PDF as text-based
            $pdf = $parser->parseFile($pdfFile->getPathname());
            $text = $pdf->getText();

            
            
            // preg_match_all('/NO\.\s*(\d+)/', $text, $matches);
            preg_match_all('/NO\.\s*([A-Za-z0-9]+)/', $text, $matches);
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
            $ocrText = (new TesseractOCR($imagePath))->run();
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
}
