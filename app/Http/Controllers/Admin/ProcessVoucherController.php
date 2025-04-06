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

use App\Jobs\ProcessBarcodeJob;
use App\Models\Barcode;
use App\Models\ProcessedBarcode;
use Illuminate\Support\Facades\Storage;
use Lukeraymonddowning\BarcodeScanner\Scanner;

use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeReader;
use Illuminate\Support\Facades\Http; // Add at top

class ProcessVoucherController extends Controller
{
    


    public function uploadAndExtractMultiplepdf(Request $request)
    {
        try {
            set_time_limit(3600);

            $request->validate([
                'pdfFiles.*' => 'required|mimes:pdf|max:40000'
            ]);

            $allBarcodes = [];

            foreach ($request->file('pdfFiles') as $pdfFile) {
                $pdfPath = $pdfFile->store('public/pdfs');
                $pdfFullPath = storage_path('app/' . $pdfPath);

                $barcodeImagePath = storage_path('app/public/barcodeimages/');
                if (!file_exists($barcodeImagePath)) {
                    mkdir($barcodeImagePath, 0777, true);
                }

                $pdf = new Pdf($pdfFullPath);
                $numberOfPages = $pdf->getNumberOfPages();
                $images = [];

                for ($i = 1; $i <= $numberOfPages; $i++) {
                    $imagePath = $barcodeImagePath . "page_{$i}_" . time() . ".jpg";
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
                            'file' => basename($imagePath),
                            'barcode' => 'Image unreadable',
                            'status' => 'Unreadable',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        continue;
                    }

                    // ✅ OCR API Call
                    $apiKey = 'K85230915088957'; // <-- Put your API Key here

                    $imageData = file_get_contents($imagePath);

                    $response = Http::attach(
                        'file', $imageData, basename($imagePath)
                    )->post('https://api.ocr.space/parse/image', [
                        'apikey' => $apiKey,
                        'language' => 'eng',
                        'isOverlayRequired' => false,
                    ]);

                    $result = $response->json();

                    if (isset($result['ParsedResults'][0]['ParsedText'])) {
                        $text = $result['ParsedResults'][0]['ParsedText'];
                    } else {
                        $text = null;
                    }

                    preg_match('/NO\.\s*(\d{6,})/', $text, $matches);

                    $barcodes[] = [
                        'file' => basename($imagePath),
                        'voucher_number' => !empty($matches[1]) ? $matches[1] : 'Not Found'
                    ];
                }

                $processVoucher = $this->processVoucher($barcodes);

                $allBarcodes[] = [
                    'pdf' => $pdfFile->getClientOriginalName(),
                    'barcodes' => $barcodes,
                    'processVoucher' => $processVoucher
                ];
            }

            return response()->json([
                'message' => 'Barcode extraction successful',
                'allBarcodes' => $allBarcodes
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong',
                'message' => $e->getMessage()
            ], 500);
        }
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



    public function uploadAndExtract_queue(Request $request)
    {
        try {
            $request->validate([
                'pdfFile' => 'required|mimes:pdf|max:40000'
            ]);

            $pdfPath = $request->file('pdfFile')->store('public/pdfs');
            $pdfFullPath = storage_path('app/' . $pdfPath);
            $barcodeImagePath = storage_path('app/public/barcodeimages/');

            if (!file_exists($barcodeImagePath)) {
                mkdir($barcodeImagePath, 0777, true);
            }

            $tesseractPath = "C:\\Program Files\\Tesseract-OCR\\tesseract.exe";

            // ✅ Dispatch Job to Queue
            ProcessBarcodeJob::dispatch($pdfFullPath, $barcodeImagePath, $tesseractPath)->onQueue('barcode_processing');

            return response()->json([
                'message' => 'File uploaded successfully! Processing in background...'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function addToProcessBarcode(Request $request)
    {
        $processBarcode = ProcessedBarcode::where('barcode', '!=', 'Not Found')->get();

        $prop = '';
        
            foreach ($processBarcode as $pdata){
                $orderDtl = Barcode::where('barcode', '=', $pdata->barcode)->first();

                if ($orderDtl) {
                    // <!-- Single Property Start -->
                    $prop.= '<tr class="item-row"><td width = "200px"><div style="color: white;  user-select:none;  padding: 5px;    background: red;    width: 45px;    display: flex;    align-items: center; margin-right:5px;   justify-content: center;    border-radius: 4px;   left: 4px;    top: 81px;" onclick="removeRow(event)" >X</div></td><td width="200px"><input style="min-width: 100px;" type="number" class="form-control donor" name="donor_acc[]" value="'.$orderDtl->user->accountno.'" placeholder="Type Acc no..."></td><td width="250px"><input style="min-width:100px" type="text" value="'.$orderDtl->user->name.'" readonly class="form-control donorAcc" value><input type="hidden" name="donor[]" value="'.$orderDtl->user_id.'"  class="donorid"></td><td width="250px"><input style="min-width:100px" name="check[]" type="text" value="'.$pdata->barcode.'" class="form-control check" ></td> <td width="20px"><input style="min-width:30px" name="amount[]" type="text" value="'.$orderDtl->amount.'" class="amount form-control" value></td><td width="250px"><input style="min-width:200px" name="note[]" type="text" class="form-control note" value></td><td width="150px"><select name="waiting[]" class="form-control"><option value="No">No</option><option value="Yes">Yes</option></select></td></tr>';
                } else {
                    // <!-- Single Property Start -->
                    $prop.= '<tr class="item-row"><td width = "200px" style="display:inline-flex;"><div style="color: white;  user-select:none;  padding: 5px;    background: red;    width: 45px;    display: flex;    align-items: center; margin-right:5px;   justify-content: center;    border-radius: 4px;   left: 4px;    top: 81px;" onclick="removeRow(event)" >X</div></td><td width="200px"><input style="min-width: 100px;" type="number" class="form-control donor" name="donor_acc[]" value="" placeholder="Type Acc no..."></td><td width="250px"><input style="min-width:100px" type="text" value="" readonly class="form-control donorAcc" value><input type="hidden" name="donor[]" value=""  class="donorid"></td><td width="250px"><input style="min-width:100px" name="check[]" type="text" value="'.$pdata->barcode.'" class="form-control check" ></td> <td width="20px"><input style="min-width:30px" name="amount[]" type="text" value="" class="amount form-control" value></td><td width="250px"><input style="min-width:200px" name="note[]" type="text" class="form-control note" value></td><td width="150px"><select name="waiting[]" class="form-control"><option value="No">No</option><option value="Yes">Yes</option></select></td></tr>';
                }
                  
            }

        return response()->json(['status'=> 300,'data'=>$prop]);
    }



    public function deleteProcessBarcode(Request $request)
    {
        try {
            // Delete all processed barcodes where 'barcode' is not 'Not Found'
            ProcessedBarcode::where('barcode', '!=', 'Not Found')->delete();

            // Delete associated images from storage
            $notReadableBarcodes = ProcessedBarcode::where('barcode', '!=', 'Not Found')->get();
            foreach ($notReadableBarcodes as $notReadable) {
                $imagePath = storage_path('app/public/barcodeimages/' . $notReadable->file);
                if (file_exists($imagePath)) {
                    unlink($imagePath); // Delete the image file
                }
            }

            return response()->json([
                'status' => 200,
                'message' => 'Processed barcodes deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => 'Something went wrong',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteProcessBarcodeImage(Request $request)
    {
        try {
            // Delete all processed barcodes where 'barcode' is not 'Not Found'
            ProcessedBarcode::where('barcode', '=', 'Not Found')->delete();
            // Delete associated images from storage
            $notReadableBarcodes = ProcessedBarcode::where('barcode', '=', 'Not Found')->get();
            foreach ($notReadableBarcodes as $notReadable) {
                $imagePath = storage_path('app/public/barcodeimages/' . $notReadable->file);
                if (file_exists($imagePath)) {
                    unlink($imagePath); // Delete the image file
                }
            }
            return response()->json([
                'status' => 200,
                'message' => 'Processed barcodes Image deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => 'Something went wrong',
                'message' => $e->getMessage()
            ], 500);
        }
    }





}
