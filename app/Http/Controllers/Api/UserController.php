<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\support\Facades\Auth;

class UserController extends Controller
{
    public function updateprofile(Request $request)
    {

        $chkemail = User::where('email','=', $request->email)->whereNotIn('id', [Auth::id()])->count();

        if( $chkemail > 0){
            $success['message'] = 'This email has already exists.';
            return response()->json(['success'=>false,'response'=> $success], 202);

        }

        if ($request->password) {
            if ($request->password != $request->cpassword) {
                $success['message'] = "Password doesn't match!!";
                return response()->json(['success'=>false,'response'=> $success], 202);
            }
        }




        $userdata= Auth::user();
        $userdata->prefix_name= $request->prefix_name;
        $userdata->name= $request->name;
        $userdata->surname= $request->surname;
        $userdata->email= $request->email;
        $userdata->phone= $request->phone;
        $userdata->houseno= $request->houseno;
        $userdata->street= $request->street;
        $userdata->address_third_line = $request->address_third_line;
        $userdata->town= $request->town;
        $userdata->postcode= $request->postcode;

            if ($request->image) {

                // unlink("images/".$userdata->photo);
                $request->validate([
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                ]);
                $rand = mt_rand(100000, 999999);
                $imageName = time(). $rand .'.'.$request->image->extension();
                $request->image->move(public_path('images'), $imageName);
                $userdata->photo= $imageName;
            }

        if ($request->password) {
            $userdata->password= Hash::make($request->password);
        }

        if ($userdata->save()) {

            $success['message'] = 'Profile Update Successfully';
            $success['data'] = $userdata;
            return response()->json(['success'=>true,'response'=> $success], 200);
        }
        else{
            
            $success['message'] = 'Server Error!!';
            return response()->json(['success'=>false,'response'=> $success], 204);
        }

    }

    public function updateprofileImage(Request $request)
    {

        // dd($request->all());
        $userdata = Auth::user();
        $userdata->name = Auth::user()->name;
        $userdata->surname = Auth::user()->surname;
        $userdata->email = Auth::user()->email;
        $userdata->phone = Auth::user()->phone;

        if ($request->image) {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            $rand = mt_rand(100000, 999999);
            $imageName = time(). $rand .'.'.$request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $userdata->photo = $imageName;
        }

        if ($userdata->save()) {

            $success['message'] = 'Profile Update Successfully';
            $success['data'] = $userdata;
            return response()->json(['success'=>true,'response'=> $success], 200);
        }
        else{
            
            $success['message'] = 'Server Error!!';
            return response()->json(['success'=>false,'response'=> $success], 204);
        }

    }
}
