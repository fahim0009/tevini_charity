<?php

    namespace App\Http\Controllers\Admin;

    use App\Http\Controllers\Controller;
    use App\Models\Barcode;
    use App\Models\Charity;
    use App\Models\Provoucher;
    use App\Models\ProvoucherBatch;
    use App\Models\User;
    use App\Models\Usertransaction;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Storage;

    use Illuminate\Support\Facades\DB;
    use thiagoalessio\TesseractOCR\TesseractOCR;
    use Spatie\PdfToImage\Pdf;
        use Imagick;
    use Illuminate\Support\Facades\Mail;
    use Illuminate\Support\Facades\Http;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\File;
    use Yajra\DataTables\Facades\DataTables;

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
        $charities = Charity::all();
        $donors = User::where([
            ['is_type', '=', 'user'],
            ['status', '=', '1']
        ])->get();

        $bid = $batches->provoucher->first()->batch_id ?? null;

        return view('batch.edit', compact('batches', 'charities', 'donors', 'bid' ));
    }

    public function pvoucherUpdate(Request $request)
    {
        $bid = $request->bid;
        $charityId = $request->charityId;
        $donorIds = $request->donorIds;
        $donorAccounts = $request->donorAccs;
        $chequeNos = $request->chqNos;
        $amounts = $request->amts;
        $notes = $request->notes;
        $waitings = $request->waitings;
        $expireds = $request->expireds;
        $batchNo = $request->batch_no;
        $batchID = $request->batchID;

        // Validation
        if (empty($charityId)) {
            return $this->errorResponse('Please select a charity first.');
        }

        if (empty($batchNo)) {
            return $this->errorResponse('Batch no is required.');
        }

        // Check for duplicate voucher entries in the request
        $duplicateCheques = array_filter(array_count_values($chequeNos), fn($count) => $count > 1);
        if (!empty($duplicateCheques)) {
            $cheque = array_key_first($duplicateCheques);
            return $this->errorResponse("Voucher $cheque is entered more than once.");
        }

        // Get the existing batch
        $probatch = ProvoucherBatch::find($batchID);
        if (!$probatch) {
            return $this->errorResponse('Batch not found.');
        }

        // Get existing vouchers in this batch
        $existingVouchers = Provoucher::where('provoucher_batch_id', $batchID)->get();
        $existingChequeNos = $existingVouchers->pluck('cheque_no')->toArray();

        // Check for already processed cheque numbers (excluding current batch)
        $allProcessedCheques = Provoucher::where('provoucher_batch_id', '!=', $batchID)
            ->pluck('cheque_no')
            ->toArray();

        foreach ($chequeNos as $chequeNo) {
            // Only check if this is a new cheque (not in existing batch)
            if (!in_array($chequeNo, $existingChequeNos)) {
                if (in_array($chequeNo, $allProcessedCheques)) {
                    return $this->errorResponse("Voucher number $chequeNo is already processed.");
                }
            }
        }

        // Check for cancelled vouchers (only for new cheques)
        foreach ($chequeNos as $chequeNo) {
            if (!in_array($chequeNo, $existingChequeNos)) {
                $isCancelled = Barcode::where('barcode', $chequeNo)->where('status', 1)->exists();
                if ($isCancelled) {
                    return $this->errorResponse("Voucher number $chequeNo is already cancelled.");
                }
            }
        }

        // Validate all required fields
        foreach ($donorIds as $index => $donorId) {
            if (empty($donorId) || empty($donorAccounts[$index]) || empty($chequeNos[$index]) || empty($amounts[$index])) {
                return $this->errorResponse('Please fill in all required fields.');
            }
        }

        // Identify deleted, updated, and new vouchers
        $requestChequeNos = $chequeNos;
        $deletedChequeNos = array_diff($existingChequeNos, $requestChequeNos);
        $newChequeNos = array_diff($requestChequeNos, $existingChequeNos);
        $updatedChequeNos = array_intersect($existingChequeNos, $requestChequeNos);

        // Process deleted vouchers - revert their balance changes
        foreach ($deletedChequeNos as $deletedChequeNo) {
            $deletedVoucher = $existingVouchers->where('cheque_no', $deletedChequeNo)->first();
            if ($deletedVoucher) {
                // If transaction was completed, revert balance
                if ($deletedVoucher->status == 1) {
                    $oldUser = User::find($deletedVoucher->user_id);
                    if ($oldUser) {
                        Charity::where('id', $deletedVoucher->charity_id)->decrement('balance', $deletedVoucher->amount);
                        $oldUser->increment('balance', $deletedVoucher->amount);

                        if ($oldUser->CreditProfileId) {
                            $this->updateCardBalance($oldUser->CreditProfileId, $oldUser->name, $deletedVoucher->amount);
                        }
                    }
                }

                // Delete transaction
                if ($deletedVoucher->tran_id) {
                    Usertransaction::find($deletedVoucher->tran_id)?->delete();
                }

                // Delete voucher
                $deletedVoucher->delete();
            }
        }

        // Calculate new total amount
        $totalBatchAmount = array_sum($amounts);

        // Update the batch
        $probatch->charity_id = $charityId;
        $probatch->batch_no = $batchNo;
        $probatch->total_amount = $totalBatchAmount;
        $probatch->save();

        // Process updated and new vouchers
        foreach ($donorIds as $index => $donorId) {
            $user = User::find($donorId);
            if (!$user) continue;

            $limit = $user->getAvailableLimit();
            $amount = $amounts[$index];
            $isPending = ($limit < $amount || $waitings[$index] === 'Yes' || $expireds[$index] == "Yes");
            $chequeNo = $chequeNos[$index];

            // Check if this is an update or new
            $existingVoucher = $existingVouchers->where('cheque_no', $chequeNo)->first();

            if ($existingVoucher) {
                // ============================================
                // UPDATE existing voucher
                // ============================================
                $oldStatus = $existingVoucher->status;
                $oldAmount = $existingVoucher->amount;
                $oldDonorId = $existingVoucher->user_id;
                $oldCharityId = $existingVoucher->charity_id;
                $newStatus = $isPending ? 0 : 1;
                $donorChanged = ($oldDonorId != $donorId);
                $amountChanged = ($oldAmount != $amount);
                $charityChanged = ($oldCharityId != $charityId);
                $statusChanged = ($oldStatus != $newStatus);

                // Revert old balance if transaction was completed and something changed
                if ($oldStatus == 1 && ($donorChanged || $amountChanged || $charityChanged || $statusChanged)) {
                    $oldUser = User::find($oldDonorId);
                    if ($oldUser) {
                        Charity::where('id', $oldCharityId)->decrement('balance', $oldAmount);
                        $oldUser->increment('balance', $oldAmount);

                        if ($oldUser->CreditProfileId) {
                            $this->updateCardBalance($oldUser->CreditProfileId, $oldUser->name, $oldAmount);
                        }
                    }
                }

                // Apply new balance if transaction should be completed
                if ($newStatus == 1 && ($donorChanged || $amountChanged || $charityChanged || $statusChanged)) {
                    Charity::where('id', $charityId)->increment('balance', $amount);
                    $user->decrement('balance', $amount);

                    if ($user->CreditProfileId) {
                        $this->updateCardBalance($user->CreditProfileId, $user->name, -$amount);
                    }
                }

                // Update voucher record
                $existingVoucher->charity_id = $charityId;
                $existingVoucher->user_id = $donorId;
                $existingVoucher->donor_acc = $donorAccounts[$index];
                $existingVoucher->amount = $amount;
                $existingVoucher->note = $notes[$index];
                $existingVoucher->waiting = $waitings[$index] ?? 'No';
                $existingVoucher->expired = $expireds[$index] ?? 'No';
                $existingVoucher->status = $newStatus;
                $existingVoucher->save();

                // Update transaction record
                if ($existingVoucher->tran_id) {
                    $transaction = Usertransaction::find($existingVoucher->tran_id);
                    if ($transaction) {
                        $transaction->user_id = $donorId;
                        $transaction->charity_id = $charityId;
                        $transaction->amount = $amount;
                        $transaction->pending = $isPending ? 0 : 1;
                        $transaction->status = $isPending ? 0 : 1;
                        $transaction->save();
                    }
                }

            } else {
                // ============================================
                // CREATE new voucher
                // ============================================
                $barcodeImagePath = $this->moveBarcodeImageAndGetPath($chequeNo);

                // Create User Transaction
                $transaction = new Usertransaction();
                $transaction->t_id = time() . "-" . $donorId;
                $transaction->user_id = $donorId;
                $transaction->charity_id = $charityId;
                $transaction->t_type = "Out";
                $transaction->amount = $amount;
                $transaction->cheque_no = $chequeNo;
                $transaction->title = "Voucher";
                $transaction->barcode_image = $barcodeImagePath;
                $transaction->pending = $isPending ? 0 : 1;
                $transaction->status = $isPending ? 0 : 1;
                $transaction->provoucher_batch_id = $probatch->id;
                $transaction->batch_no = $batchNo;
                $transaction->save();

                // Create ProVoucher record
                $voucher = new Provoucher();
                $voucher->batch_no = $batchNo;
                $voucher->provoucher_batch_id = $probatch->id;
                $voucher->charity_id = $charityId;
                $voucher->user_id = $donorId;
                $voucher->donor_acc = $donorAccounts[$index];
                $voucher->cheque_no = $chequeNo;
                $voucher->amount = $amount;
                $voucher->note = $notes[$index];
                $voucher->waiting = $waitings[$index] ?? 'No';
                $voucher->expired = $expireds[$index] ?? 'No';
                $voucher->status = $isPending ? 0 : 1;
                $voucher->tran_id = $transaction->id;
                $voucher->save();

                // Update balances if transaction is complete
                if (!$isPending) {
                    Charity::where('id', $charityId)->increment('balance', $amount);
                    $user->decrement('balance', $amount);

                    if ($user->CreditProfileId) {
                        $this->updateCardBalance($user->CreditProfileId, $user->name, -$amount);
                    }
                }
            }
        }

        return response()->json([
            'status' => 300,
            'message' => $this->successMessage('Voucher updated successfully.'),
            'charity_id' => $charityId,
            'batch_id' => $bid,
        ]);
    }



        private function moveBarcodeImageAndGetPath(string $barcode): ?string
    {
        $processedBarcode = \App\Models\ProcessedBarcode::where('barcode', $barcode)->first();

        if (!$processedBarcode || !$processedBarcode->file) {
            return null;
        }

        $sourcePath = public_path('storage/barcodeimages/' . $processedBarcode->file);
        $destinationDir = public_path('images/barcodeimage');
        $destinationPath = $destinationDir . '/' . $processedBarcode->file;

        if (!\File::exists($destinationDir)) {
            \File::makeDirectory($destinationDir, 0777, true);
        }

        if (\File::exists($sourcePath)) {
            \File::move($sourcePath, $destinationPath);
            return 'images/barcodeimage/' . $processedBarcode->file; // path relative to public/
        }

        return null;
    }


    private function errorResponse($message)
    {
        return response()->json([
            'status' => 303,
            'message' => "<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>$message</b></div>"
        ]);
    }

    private function successMessage($message)
    {
        return "<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>$message</b></div>";
    }

    private function updateCardBalance($profileId, $profileName, $balanceChange)
    {
        Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
            ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product/updateCreditProfile/availableBalance', [
                'CreditProfileId' => $profileId,
                'CreditProfileName' => $profileName,
                'AvailableBalance' => $balanceChange,
                'comment' => 'Pending Voucher Balance update',
            ]);
    }









    

}
