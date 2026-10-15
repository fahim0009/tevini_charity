<?php

namespace App\Http\Controllers\Donor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\StandingDonation;
use App\Models\StandingdonationDetail;
use App\Models\Donation;
use App\Models\Usertransaction;

use App\DTOs\OnlineDonationData;
use App\Exceptions\Donation\DonationException;
use App\Services\Donation\OnlineDonationService;

class StandingDonationController extends Controller
{
    public function userStantingDonationStore(Request $request)
    {
        try {
            $data = OnlineDonationData::fromDonorWebStanding(
                donorId:        (int) $request->userid,
                charityId:      (int) $request->charity_id,
                amount:         (float) $request->amount,
                isAnonymous:    $request->boolean('ano_donation'),
                charityNote:    $request->charitynote,
                myNote:         $request->mynote,
                confirmDonation: $request->c_donation !== 'false',
                paymentsType:    $request->payments_type,
                numberPayments:  $request->number_payments ? (int) $request->number_payments : null,
                startingDate:    $request->starting,
                intervalValue:   (int) $request->interval,
            );

            $standing = app(OnlineDonationService::class)->createStandingDonation($data);

            $message = "<div class='alert alert-success'><b>Standing order donation submitted successfully.</b></div>";
            return response()->json(['status' => 300, 'message' => $message]);

        } catch (DonationException $e) {
            $message = "<div class='alert alert-danger'><b>{$e->getUserMessage()}</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 303,
                'message' => "<div class='alert alert-danger'><b>Something went wrong.</b></div>"
            ]);
        }
    }

    public function activeStandinguser(Request $request)
    {
        $original = StandingDonation::where('id', $request->id)
                                    ->where('user_id', Auth::user()->id)
                                    ->first();

        if (!$original) {
            return response()->json(['status' => 404, 'message' => 'Record not found']);
        }

        if ($request->status == 1) {
            $newDonation = $original->replicate();
            
            // Apply edited data from the modal form
            $newDonation->amount = $request->amount;
            $newDonation->starting = $request->starting;
            $newDonation->payments = $request->payments;
            $newDonation->number_payments = $request->payments == "1" ? $request->number_payments : null;
            $newDonation->interval = $request->interval;
            $newDonation->charitynote = $request->charitynote;
            $newDonation->mynote = $request->mynote;
            
            $newDonation->status = 1;
            $newDonation->created_at = now();
            $newDonation->updated_at = now();
            $newDonation->save();

            $message = "<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b> Donation Re-Activated Successfully.</b></div>";
            return response()->json(['status' => 300, 'message' => $message]);

        } else {
            $original->status = 0;
            $original->save();
            
            $message = "<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Inactive Successfully.</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }
    }

    public function userDonationrecod()
    {
        $donation = Donation::where([
            ['standing_order','=', 'false'],
            ['user_id','=', auth()->user()->id]
        ])->get();

        $data = Usertransaction::with('donation','standingDonations')
                ->where(function($q) {
                    $q->whereNotNull('donation_id')
                    ->orWhereNotNull('standing_donationdetails_id');
                })
                ->where('status', 1)
                ->where('user_id', auth()->user()->id)
                ->latest()->limit(5)
                ->get();    

        return view('frontend.user.donationrecord')
            ->with('donation', $donation);
    }

    public function userStandingrecod()
    {
        // Get all standing donations for the user with their detail counts
        $donations = StandingDonation::with('standingdonationDetail')
            ->withCount('standingdonationDetail as donation_count')
            ->where('user_id', auth()->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Check for completion (status = 2)
        foreach ($donations as $donation) {
            // If it's a fixed payment plan, not already completed, and payments made >= number_payments
            if (
                $donation->payments == 1 && 
                !is_null($donation->number_payments) && 
                $donation->donation_count >= $donation->number_payments && 
                $donation->status != 2
            ) {
                $donation->status = 2; // 2 = Completed
                $donation->save();
            }
        }

        return view('frontend.user.standingdonationrecord', compact('donations'));
    }

    


    public function usersingleStanding($id)
    {

        $singleStddonation = StandingdonationDetail::where([
            ['standing_donation_id','=', $id],
            ['user_id','=', auth()->user()->id]
        ])->orderBy('id','DESC')->get();

        return view('frontend.user.singlestandingrecord',compact('singleStddonation'));

    }



    public function userStandingDonationUpdate(Request $request)
    {
        // 1. Basic Validation (matching your original logic)
        if(empty($request->amount)){
            $message = "<div class='alert alert-danger'><b>Please fill amount field.</b></div>";
            return response()->json(['status'=> 303, 'message'=>$message]);
        }

        if(($request->payments == "1") && (empty($request->number_payments))){
            $message = "<div class='alert alert-danger'><b>Please fill number of payments field.</b></div>";
            return response()->json(['status'=> 303, 'message'=>$message]);
        }

        // 2. Find the donation and ensure it belongs to the logged-in user
        $existing = StandingDonation::where('id', $request->donation_id)
            ->where('user_id', auth()->user()->id)
            ->first();

        if(!$existing) {
            $message = "<div class='alert alert-danger'><b>Donation not found or access denied.</b></div>";
            return response()->json(['status'=> 303, 'message'=>$message]);
        }

        try {
            // 3. Build the DTO using the update request data
            // We use the existing record to keep fields that shouldn't change (like charity_id, anonymous status)
            $data = OnlineDonationData::fromDonorWebStanding(
                donorId:        (int) auth()->user()->id,
                charityId:      (int) $existing->charity_id,
                amount:         (float) $request->amount,
                isAnonymous:    $existing->ano_donation === 'true',
                charityNote:    $request->charitynote,
                myNote:         $request->mynote,
                confirmDonation: true, // Already confirmed on creation
                paymentsType:    $request->payments,
                numberPayments:  $request->payments == "1" ? (int) $request->number_payments : null,
                startingDate:    $request->starting,
                intervalValue:   (int) $request->interval,
            );

            // 4. Call the Service to handle the update logic
            app(OnlineDonationService::class)->updateStandingDonation($existing->id, $data);

            $message = "<div class='alert alert-success'><b>Standing order updated successfully.</b></div>";
            return response()->json(['status'=> 300, 'message'=>$message]);

        } catch (DonationException $e) {
            $message = "<div class='alert alert-danger'><b>{$e->getUserMessage()}</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        } catch (\Throwable $e) {
            $message = "<div class='alert alert-danger'><b>Failed to update standing order.</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }
    }
}