<?php

namespace App\Http\Controllers\Admin;

use App\Models\SeoSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\support\Facades\Auth;

class SeoSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $seo = SeoSetting::all()->first();
        // dd($company); exit();
        return view('seo.index',compact('seo'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $seo = new SeoSetting();
            $seo->keyword= $request->keyword;
            $seo->author= $request->author;
            $seo->revisit= $request->revisit;
            $seo->sitemap_link= $request->sitemap_link;
            $seo->description= $request->description;
            $seo->created_by= Auth::user()->id;
            $seo->save();

            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Created Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);

        }catch (\Exception $e){
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SeoSetting  $seoSetting
     * @return \Illuminate\Http\Response
     */
    public function show(SeoSetting $seoSetting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SeoSetting  $seoSetting
     * @return \Illuminate\Http\Response
     */
    public function edit(SeoSetting $seoSetting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SeoSetting  $seoSetting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $seo = SeoSetting::find($id);
       
        $seo->keyword= $request->keyword;
        $seo->author= $request->author;
        $seo->revisit= $request->revisit;
        $seo->sitemap_link= $request->sitemap_link;
        $seo->description= $request->description;

        if ($seo->save()) {

            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Updated Successfully.</b></div>";

            return response()->json(['status'=> 300,'message'=>$message]);
        }else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SeoSetting  $seoSetting
     * @return \Illuminate\Http\Response
     */
    public function destroy(SeoSetting $seoSetting)
    {
        //
    }
}
