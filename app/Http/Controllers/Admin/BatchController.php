<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProvoucherBatch;
use App\Models\Usertransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

    public function uploadPdf(Request $request) {
        $request->validate([
            'batch_id' => 'required|exists:batches,id',
            'pdf_file' => 'required|mimes:pdf|max:2048', // 2MB limit
        ]);

        $batch = ProvoucherBatch::find($request->batch_id);
        
        if ($request->hasFile('pdf_file')) {
            $path = $request->file('pdf_file')->store('batch_pdfs', 'public');
            
            // Update your database column (e.g., pdf_path)
            $batch->update(['pdf_path' => $path]);

            return response()->json(['message' => 'File uploaded successfully!'], 200);
        }

        return response()->json(['message' => 'No file found.'], 400);
    }


}
