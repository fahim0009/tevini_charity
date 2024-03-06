<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TdfTransaction;
use Illuminate\Http\Request;

class TDFTransactionController extends Controller
{
    public function getTDFTransaction(Request $request)
    {
        $data = TdfTransaction::orderby('id','DESC')->get();
        return view('admin.tdf.transaction', compact('data'));
    }
}
