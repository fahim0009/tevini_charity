<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('report.index');
    }


    public function userReport()
    {
        return view('frontend.user.report');
    }

    public function userGivingReport()
    {
        return view('frontend.user.giving');
    }
}
