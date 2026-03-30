<?php

    namespace App\Http\Controllers\Admin;

    use App\Http\Controllers\Controller;
use App\Models\Charity;
use App\Models\ProvoucherBatch;
use App\Models\User;
use App\Models\Usertransaction;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Storage;

    use Illuminate\Support\Facades\DB;
    use thiagoalessio\TesseractOCR\TesseractOCR;
    use Spatie\PdfToImage\Pdf;
    use Imagick;

class BatchController extends Controller
{
    public function index()
    {
        
        $batches = ProvoucherBatch::with(['transaction', 'provoucher'])
                    ->latest()
                    ->get();


        return view('batch.index', compact('batches'));
    }

    public function uploadBarcode(Request $request)
    {
        $request->validate([
            'barcode_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $transaction = Usertransaction::find($request->id);

        if ($request->hasFile('barcode_image')) {
            // Optional: Delete old image if it exists
            if ($transaction->barcode_image && file_exists(public_path($transaction->barcode_image))) {
                unlink(public_path($transaction->barcode_image));
            }

            $image = $request->file('barcode_image');
            $name = time() . '_' . $image->getClientOriginalName();
            $destinationPath = public_path('/images/barcodes');
            $image->move($destinationPath, $name);
            
            $transaction->barcode_image = 'images/barcodes/' . $name;
            $transaction->save();

            return response()->json([
                'success' => true,
                'image_url' => asset($transaction->barcode_image)
            ]);
        }

        return response()->json(['success' => false], 400);
    }


    public function uploadPdf(Request $request)
    {
        $request->validate([
            'batch_id' => 'required|exists:provoucher_batches,id',
            'pdf_file' => 'required|mimes:pdf|max:10048',
        ]);

        set_time_limit(300);

        // $tesseractPath = "C:\\Program Files\\Tesseract-OCR\\tesseract.exe";
        
        $tesseractPath = "/usr/bin/tesseract";

        // ✅ Store PDF (keep as-is or move to public if needed)
        $pdfPath = $request->file('pdf_file')->store('public/pdfs');
        $pdfFullPath = storage_path('app/' . $pdfPath);

        // ✅ NEW: Use public folder (same as your barcode_image upload)
        $imageFolder = public_path('/images/barcodes/');
        if (!file_exists($imageFolder)) {
            mkdir($imageFolder, 0777, true);
        }

        $batch = DB::table('provoucher_batches')
            ->where('id', $request->batch_id)
            ->first();

        $pdf = new Pdf($pdfFullPath);
        $totalPages = $pdf->getNumberOfPages();

        for ($i = 1; $i <= $totalPages; $i++) {

            // ✅ Generate image name
            $imageName = "batch_{$request->batch_id}_page_{$i}_" . time() . ".jpg";
            $tempImagePath = storage_path('app/temp_' . $imageName); // temp save
            $finalImagePath = $imageFolder . $imageName;

            // ✅ Save from PDF (temporary)
            $pdf->setPage($i)->saveImage($tempImagePath);

            if (!$this->isValidImage($tempImagePath)) {
                unlink($tempImagePath);
                continue;
            }

            // ✅ Move to public/images/barcodes
            rename($tempImagePath, $finalImagePath);

            // ✅ OCR
            try {
                $text = (new TesseractOCR($finalImagePath))
                    ->executable($tesseractPath)
                    ->lang('eng')
                    ->psm(6)
                    ->oem(1)
                    ->run();
            } catch (\Exception $e) {
                continue;
            }

            // ✅ Extract number
            if (preg_match('/NO\.\s*(\d{6,})/', $text, $matches)) {
                $number = $matches[1];
            } elseif (preg_match('/\b\d{6,}\b/', $text, $matches)) {
                $number = $matches[0];
            } else {
                $number = 'Not Found';
            }

            // ✅ Update transaction
            Usertransaction::where('provoucher_batch_id', $request->batch_id)
                ->where('cheque_no', $number)
                ->update(['barcode_image' => 'images/barcodes/' . $imageName]);

            DB::table('processed_barcodes')->insert([
                'file'       => 'images/barcodes/' . $imageName, 
                'barcode'    => $number,
                'provoucher_batch_id' => $batch->id ?? null,
                'batch_no'            => $batch->batch_no ?? null,
                'status'     => 'Processed',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json([
            'message' => 'PDF processed successfully'
        ]);
    }

    private function isValidImage($imagePath)
    {
        $image = new \Imagick($imagePath);

        if ($image->getImageWidth() < 100 || $image->getImageHeight() < 100) {
            return false;
        }

        if ($image->getImageLength() < 5000) {
            return false;
        }

        return true;
    }


    public function edit($id)
    {
        
        $batches = ProvoucherBatch::with(['transaction', 'provoucher'])->where('id', $id)->first();
        // dd( $batches );

        $charities = Charity::all();
        $donors = User::where([
            ['is_type', '=', 'user'],
            ['status', '=', '1']
        ])->get();

        return view('batch.edit', compact('batches', 'charities', 'donors' ));
    }

}
