<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AboutContent;
use App\Models\AboutHelp;
use App\Models\CompanyDetail;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function index()
    {
        return view('frontend.aboutus');
    }

    public function terms()
    {
        return view('frontend.terms-condition');
    }

    public function privacy()
    {
        return view('frontend.privacy');
    }

    public function declaration()
    {
        return view('frontend.declaration');
    }

    public function cardterms()
    {
        return view('frontend.cardterms-condition');
    }

    public function aboutHelp()
    {
        $abouthelp = AboutHelp::all();
        return view('about.abouthelp',compact('abouthelp'));
    }

    public function aboutHelpStore(Request $request)
        {
            
            $data = new AboutHelp;
            $data->title= $request->title;
            if ($request->image) {
            
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            $rand = mt_rand(100000, 999999);
            $imageName = time(). $rand .'.'.$request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $data->image= $imageName;
            }
            
            $data->description= $request->description;


            if ($data->save()) {
                $message ="Charity Created Successfully";
                return back()->with('message', $message);
            }
            else{
                $error ="Server problem";
                return back()->with('error', $error);
            }
        }


        public function abouthelpedit($id)
    {
        
        $about = AboutHelp::where('id','=' ,decrypt($id))->first();
        return view('about.editabouthelp', compact('about'));
    }

    public function abouthelpupdate(Request $request, $id)
    {
        
       
        $data = AboutHelp::findOrFail($id);
        $data->title = $request->title;
        if ($request->image) {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            $rand = mt_rand(100000, 999999);
            $imageName = time(). $rand .'.'.$request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $data->image= $imageName;
        }


        $data->description = $request->description;
        if($data->save()){

            $message ="About help Update Successfully";

        return redirect()->route('about.help')->with(['status'=> 303,'message'=> $message]);
        }
        return back()->with(['status'=> 303,'message'=>'Server Error!!']);

    }

    public function abouthelpdelete($id)
    {
        if( AboutHelp::destroy($id)){
            return response()->json(['success'=>true,'message'=>'About Help has been deleted successfully']);
        }else{
            return response()->json(['success'=>false,'message'=>'Delete Failed']);
        }
    }

    public function aboutcontentshow()
    {
        $about = AboutContent::all();
        return view('about.aboutcontent',compact('about'));
    }

    public function aboutcontentedit($id)
    {
        
        $about = AboutContent::where('id','=' ,decrypt($id))->first();
        return view('about.editaboutcontent', compact('about'));
    }

    public function aboutcontentupdate(Request $request, $id)
    {
        
       
        $data = AboutContent::findOrFail($id);
        $data->title1 = $request->title1;
        $data->title2 = $request->title2;
        $data->title3 = $request->title3;
        $data->turnover_title = $request->turnover_title;
        $data->profit_title = $request->profit_title;

        $request->validate([
            'turnover_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $rand = mt_rand(100000, 999999);
        $imageName = time(). $rand .'.'.$request->turnover_image->extension();
        $request->turnover_image->move(public_path('images'), $imageName);
        $data->turnover_image= $imageName;

        $request->validate([
            'profit_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $rand = mt_rand(100000, 999999);
        $imageName = time(). $rand .'.'.$request->profit_image->extension();
        $request->profit_image->move(public_path('images'), $imageName);
        $data->profit_image= $imageName;

        if($data->save()){

            $message ="About content Update Successfully";

        return redirect()->route('aboutcontent.show')->with(['status'=> 303,'message'=> $message]);
        }
        return back()->with(['status'=> 303,'message'=>'Server Error!!']);

    }


    public function whyuseus()
    {
        return view('frontend.whyuseus');
    }

    public function team()
    {
        return view('frontend.team');
    }

    public function blog()
    {
        return view('frontend.blog');
    }

    // app version check
    public function appVersion()
    {
        
        $data = CompanyDetail::select('id','app_version', 'version_category')->first(); 
        
        if($data == null){
            $data = 'Data Not Found';
        }
        $responseArray = [
            'status'=>'ok',
            'data'=>$data
        ]; 
        return response()->json($responseArray,200);
    }
}
