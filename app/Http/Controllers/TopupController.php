<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StripeTopup;
use Yajra\DataTables\Facades\DataTables;

class TopupController extends Controller
{
    public function stripetopup(Request $request)
    {
        if ($request->ajax()) {
            $data = StripeTopup::with('donor')->orderby('id', 'desc');

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('date', function($row){
                    return $row->created_at->format('d/m/Y');
                })
                ->addColumn('donor_account', function($row){
                    return $row->donor ? $row->donor->accountno : 'N/A';
                })
                ->addColumn('amount_formatted', function($row){
                    return '£' . $row->amount;
                })
                ->addColumn('topup_link', function($row){
                    $url = route('topup', [$row->donor_id, '0']);
                    return '<a href="'.$url.'">TopUp</a>';
                })
                ->addColumn('status_dropdown', function($row){
                    $disabled = ($row->status == "1") ? 'disabled' : '';
                    $pendingSelected = ($row->status == "0") ? 'selected' : '';
                    $completeSelected = ($row->status == "1") ? 'selected' : '';
                    
                    return '
                        <select class="form-control status-change" data-id="'.$row->id.'" '.$disabled.'>
                            <option value="0" '.$pendingSelected.'>Pending</option>
                            <option value="1" '.$completeSelected.'>Complete</option>
                        </select>';
                })
                ->rawColumns(['topup_link', 'status_dropdown'])
                ->make(true);
        }

        return view('stripe.stripetopup');
    }

}
