<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Provoucher;
use App\Models\Charity;
use App\Models\Barcode;
use App\Models\User;
use App\Models\Batchprov;
use App\Models\Usertransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Services\BarcodeExtractionService;

class CharityApiController extends Controller
{
    /**
     * Get pending vouchers with transaction data
     * URL: GET /api/charity/pending-voucher/{id}
     */
    public function pendingVoucher($id)
    {
        // Verify charity exists
        $charity = Charity::find($id);

        if ($charity == null) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Charity not found',
                'data'    => null
            ], 404);
        }

        // Get pending vouchers with transaction & user relations
        $cvouchers = Provoucher::with([
                'transaction' => function ($query) {
                    $query->select([
                        'id', 't_id', 'user_id', 'charity_id', 't_type',
                        'amount', 'cheque_no', 'title', 'barcode_image',
                        'pending', 'status', 'expired', 'batch_no',
                        'provoucher_batch_id', 'created_at'
                    ]);
                },
                'user' => function ($query) {
                    $query->select(['id', 'name', 'accountno']);
                }
            ])
            ->where([
                ['charity_id', '=', $id],
                ['waiting',    '=', 'No'],
                ['status',     '=', '0']
            ])
            ->select([
                'id', 'charity_id', 'user_id', 'batch_id', 'batch_no',
                'provoucher_batch_id', 'donor_acc', 'cheque_no',
                'voucher_type', 'amount', 'note', 'waiting',
                'expired', 'status', 'tran_id', 'created_at'
            ])
            ->orderBy('id', 'DESC')
            ->get();

        if ($cvouchers->isEmpty()) {
            return response()->json([
                'status'  => 'ok',
                'message' => 'No pending vouchers found',
                'data'    => []
            ], 200);
        }

        // Format the response with custom structure
        $data = $cvouchers->map(function ($voucher) {
            return [
                // Voucher fields
                'voucher_id'         => $voucher->id,
                'charity_id'         => $voucher->charity_id,
                'batch_id'           => $voucher->batch_id,
                'batch_no'           => $voucher->batch_no,
                'donor_acc'          => $voucher->donor_acc,
                'cheque_no'          => $voucher->cheque_no,
                'voucher_type'       => $voucher->voucher_type,
                'amount'             => $voucher->amount,
                'note'               => $voucher->note,
                'waiting'            => $voucher->waiting,
                'expired'            => $voucher->expired,
                'status'             => $voucher->status,
                'created_at'         => $voucher->created_at,

                // Donor info (from accessor + user relation)
                'donor_name'         => $voucher->donor_name,
                'donor_account_no'   => $voucher->account_no,

                // Transaction data (from relation)
                'transaction'        => $voucher->transaction ? [
                    'id'                 => $voucher->transaction->id,
                    't_id'               => $voucher->transaction->t_id,
                    't_type'             => $voucher->transaction->t_type,
                    'amount'             => $voucher->transaction->amount,
                    'cheque_no'          => $voucher->transaction->cheque_no,
                    'title'              => $voucher->transaction->title,
                    'barcode_image'      => $voucher->transaction->barcode_image,
                    'pending'            => $voucher->transaction->pending,
                    'status'             => $voucher->transaction->status,
                    'expired'            => $voucher->transaction->expired,
                    'batch_no'           => $voucher->transaction->batch_no,
                    'created_at'         => $voucher->transaction->created_at,
                ] : null,
            ];
        });

        return response()->json([
            'status' => 'ok',
            'count'  => $cvouchers->count(),
            'data'   => $data
        ], 200);
    }


    /**
     * ✅ Barcode lookup API
     * Accepts EITHER:
     *   - barcode (text field)        → use directly
     *   - barcode_image (file upload)  → extract barcode from image first
     *
     * URL: POST /api/charity/charity-barcode
     */
    public function getCharitybarCode(Request $request)
    {
        try {
            // ============================================
            // STEP 1: Validate input (barcode OR barcode_image)
            // ============================================
            $validator = Validator::make($request->all(), [
                'barcode'       => 'nullable|string',
                'barcode_image' => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'status'  => 303,
                    'message' => 'Invalid input.',
                    'errors'  => $validator->errors()
                ], 422);
            }

            $barcodeValue = $request->input('barcode');
            $source = 'manual';  // Track where the barcode came from

            // ============================================
            // STEP 2: If image uploaded, extract barcode from image
            // ============================================
            if ($request->hasFile('barcode_image')) {
                $image = $request->file('barcode_image');
                $imagePath = $image->store('public/barcodeimages');
                $fullPath = storage_path('app/' . $imagePath);

                // Use the service to extract barcode
                $service = new BarcodeExtractionService();
                $extractedBarcode = $service->extractBarcodeFromImage($fullPath);

                if (!$extractedBarcode) {
                    // Clean up uploaded image
                    @unlink($fullPath);
                    return response()->json([
                        'success' => false,
                        'status'  => 303,
                        'message' => 'Could not extract barcode from the uploaded image. Please try entering the barcode manually.'
                    ], 200);
                }

                $barcodeValue = $extractedBarcode;
                $source = 'image';
            }

            // ============================================
            // STEP 3: Validate we have a barcode value
            // ============================================
            if (!$barcodeValue) {
                return response()->json([
                    'success' => false,
                    'status'  => 303,
                    'message' => 'Please provide either a barcode or upload a barcode image.'
                ], 422);
            }

            // ============================================
            // STEP 4: Look up barcode in DB
            // ============================================
            $barcode = Barcode::with(['user', 'orderhistory.voucher'])
                ->where('barcode', $barcodeValue)
                ->first();

            if (!$barcode) {
                return response()->json([
                    'success' => false,
                    'status'  => 303,
                    'message' => "No data found for barcode: {$barcodeValue}"
                ], 404);
            }

            $voucher = $barcode->orderhistory?->voucher ?? null;

            // ============================================
            // STEP 5: Check if voucher is already processed
            // ============================================
            $chequeNo = $voucher?->cheque_no
                        ?? $barcode->orderhistory?->cheque_no
                        ?? $barcode->cheque_no
                        ?? null;

            if ($chequeNo) {
                $alreadyProcessed = Provoucher::where('cheque_no', $chequeNo)->exists();

                if ($alreadyProcessed) {
                    return response()->json([
                        'success'     => false,
                        'status'      => 303,
                        'vouchertype' => $voucher?->type,
                        'cheque_no'   => $chequeNo,
                        'barcode'     => $barcodeValue,
                        'message'     => "Voucher number {$chequeNo} is already processed."
                    ], 200);
                }
            }

            // ============================================
            // STEP 6: Check invalid amounts
            // ============================================
            $invalidAmounts = [0.50, 1.00, 2.00];
            if ($voucher && in_array($voucher->single_amount, $invalidAmounts)) {
                return response()->json([
                    'success'     => false,
                    'status'      => 303,
                    'vouchertype' => $voucher->type,
                    'message'     => 'This voucher will not be accepted.'
                ], 200);
            }

            // ============================================
            // STEP 7: Check blank voucher
            // ============================================
            if ($voucher && $voucher->type === "Blank") {
                return response()->json([
                    'success'     => false,
                    'status'      => 303,
                    'vouchertype' => $voucher->type,
                    'message'     => "This is a blank voucher. You can't process it."
                ], 200);
            }

            // ============================================
            // STEP 8: Get user limit and determine status
            // ============================================
            $user = User::find($barcode->user_id);
            $limitChk = $user ? ($user->getAvailableLimit() ?? 0) : 0;

            $barcodeStatus = ($limitChk < $barcode->amount)
                ? 'Will be pending'
                : 'Will be complete';

            // ============================================
            // STEP 9: Return success response
            // ============================================
            return response()->json([
                'success'       => true,
                'status'        => 300,
                'donorname'     => $barcode->user?->name ?? 'Unknown',
                'donorid'       => $barcode->user_id,
                'donoracc'      => $barcode->user?->accountno ?? 'N/A',
                'amount'        => $barcode->amount,
                'barcodeStatus' => $barcodeStatus,
                'vouchertype'   => $voucher?->type,
                'cheque_no'     => $chequeNo,
                'barcode'       => $barcode->barcode,
                'source'        => $source,
                'message'       => 'Barcode details retrieved successfully.'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Barcode API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'status'  => 500,
                'message' => 'Server Error: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * ✅ Store/process multiple vouchers
     * URL: POST /api/pvoucher-store
     *
     * Expected JSON body:
     * {
     *     "charity_id": 1,
     *     "vouchers": [
     *         {
     *             "donor_id": 15,
     *             "donor_acc": "99887",
     *             "cheque_no": "20241",
     *             "amount": "15.00",
     *             "note": "Donation for food",
     *             "waiting": "No"
     *         },
     *         {
     *             "donor_id": 16,
     *             "donor_acc": "99888",
     *             "cheque_no": "20242",
     *             "amount": "20.00",
     *             "note": "",
     *             "waiting": "No"
     *         }
     *     ]
     * }
     */
    public function pvoucherStore(Request $request)
    {
        try {
            // ============================================
            // STEP 1: Validate input
            // ============================================
            $validator = Validator::make($request->all(), [
                'charity_id'           => 'required|integer|exists:charities,id',
                'vouchers'             => 'required|array|min:1',
                'vouchers.*.donor_id'  => 'required|integer|exists:users,id',
                'vouchers.*.donor_acc' => 'required|string',
                'vouchers.*.cheque_no' => 'required|string',
                'vouchers.*.amount'    => 'required|numeric|min:0.01',
                'vouchers.*.note'      => 'nullable|string',
                'vouchers.*.waiting'   => 'nullable|in:Yes,No',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'status'  => 303,
                    'message' => 'Validation failed.',
                    'errors'  => $validator->errors()
                ], 422);
            }

            $charity_id = $request->input('charity_id');
            $vouchers   = $request->input('vouchers');

            // ============================================
            // STEP 2: Extract cheque numbers for validation
            // ============================================
            $chequeNos = array_column($vouchers, 'cheque_no');

            // ============================================
            // STEP 3: Check duplicate cheque numbers in submission
            // ============================================
            $chequeCounts = array_count_values($chequeNos);
            foreach ($chequeCounts as $chequeNo => $count) {
                if ($count > 1) {
                    return response()->json([
                        'success' => false,
                        'status'  => 303,
                        'message' => "Voucher {$chequeNo} is entered more than once."
                    ], 200);
                }
            }

            // ============================================
            // STEP 4: Check already processed cheques in DB
            // ============================================
            $existingCheques = Provoucher::whereIn('cheque_no', $chequeNos)
                ->pluck('cheque_no')
                ->toArray();

            if (!empty($existingCheques)) {
                $duplicateList = implode(', ', $existingCheques);
                return response()->json([
                    'success'       => false,
                    'status'        => 303,
                    'message'       => 'Voucher number(s) already processed.',
                    'cheque_nos'    => $existingCheques
                ], 200);
            }

            // ============================================
            // STEP 5: Process vouchers using DB transaction
            // ============================================
            DB::beginTransaction();

            try {
                // Create new batch
                $batch = new Batchprov();
                $batch->charity_id = $charity_id;
                $batch->status = 0;
                $batch->save();

                $processedVouchers = [];
                $totalAmount = 0;

                foreach ($vouchers as $key => $voucher) {
                    $donor_id   = $voucher['donor_id'];
                    $donor_acc  = $voucher['donor_acc'];
                    $cheque_no  = $voucher['cheque_no'];
                    $amount     = $voucher['amount'];
                    $note       = $voucher['note'] ?? '';
                    $waiting    = $voucher['waiting'] ?? 'No';

                    $user = User::find($donor_id);
                    if (!$user) {
                        throw new \Exception("Donor with ID {$donor_id} not found.");
                    }

                    $limitChk = $user->getAvailableLimit() ?? 0;

                    // Determine status
                    $isPending = ($limitChk < $amount);

                    // Create user transaction
                    $utransaction = new Usertransaction();
                    $utransaction->t_id       = time() . "-" . $donor_id;
                    $utransaction->user_id    = $donor_id;
                    $utransaction->charity_id = $charity_id;
                    $utransaction->t_type     = "Out";
                    $utransaction->amount     = $amount;
                    $utransaction->cheque_no   = $cheque_no;
                    $utransaction->title       = "Voucher";
                    $utransaction->pending     = $isPending ? 0 : 1;
                    $utransaction->status      = $isPending ? 0 : 1;
                    $utransaction->save();

                    // Save into provoucher
                    $pvsr = new Provoucher();
                    $pvsr->charity_id = $charity_id;
                    $pvsr->user_id    = $donor_id;
                    $pvsr->batch_id   = $batch->id;
                    $pvsr->donor_acc  = $donor_acc;
                    $pvsr->cheque_no  = $cheque_no;
                    $pvsr->amount     = $amount;
                    $pvsr->note       = $note;
                    $pvsr->waiting    = $waiting;
                    $pvsr->status     = $isPending ? 0 : 1;
                    $pvsr->tran_id    = $utransaction->id;
                    $pvsr->save();

                    // Update charity and user balances if complete
                    if (!$isPending) {
                        $charity = Charity::find($charity_id);
                        $charity->increment('balance', $amount);
                        $user->decrement('balance', $amount);
                        $totalAmount += $amount;
                    }

                    $processedVouchers[] = [
                        'voucher_id'      => $pvsr->id,
                        'transaction_id'  => $utransaction->id,
                        'donor_id'         => $donor_id,
                        'donor_name'       => $user->name,
                        'donor_acc'        => $donor_acc,
                        'cheque_no'        => $cheque_no,
                        'amount'           => $amount,
                        'note'             => $note,
                        'waiting'          => $waiting,
                        'status'           => $isPending ? 'Pending' : 'Complete',
                    ];
                }

                DB::commit();

                // ============================================
                // STEP 6: Return success response
                // ============================================
                return response()->json([
                    'success'            => true,
                    'status'             => 300,
                    'message'            => 'Vouchers processed successfully.',
                    'charity_id'         => $charity_id,
                    'batch_id'           => $batch->id,
                    'total_vouchers'     => count($processedVouchers),
                    'total_amount'       => $totalAmount,
                    'processed_vouchers' => $processedVouchers
                ], 200);

            } catch (\Exception $e) {
                // Rollback everything if anything fails
                DB::rollBack();
                Log::error('Voucher store error: ' . $e->getMessage());

                return response()->json([
                    'success' => false,
                    'status'  => 500,
                    'message' => 'Failed to process vouchers. All changes rolled back.',
                    'error'   => $e->getMessage()
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Voucher store API error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'status'  => 500,
                'message' => 'Server Error: ' . $e->getMessage()
            ], 500);
        }
    }


}