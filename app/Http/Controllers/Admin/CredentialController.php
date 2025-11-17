<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserDetail;
use Illuminate\Http\Request;

class CredentialController extends Controller
{
    public function newUserCredentialStore(Request $request)
    {
        $request->validate([
            'newemail' => 'required|email|max:255|unique:user_details,email',
        ]);
        $newemail = $request->newemail;

        $data = new UserDetail();
        $data->date = date('Y-m-d');
        $data->email = $request->newemail;
        $data->user_id = $request->donor_id;
        $data->charity_id = $request->charity_id;
        $data->email_verified_at = now();
        $data->save();
        return back()->with('success', 'New email add successfully.');
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'email' => 'required|email|max:255|unique:user_details,email,' . $id,
        ]);

        $data = UserDetail::findOrFail($id);
        $data->email = $request->email;
        $data->save();


        return redirect()->back()->with('success', 'Email updated successfully.');
    }

    // ✅ Delete email
    public function destroy($id)
    {
        $data = UserDetail::findOrFail($id);
        $data->delete();

        return redirect()->back()->with('success', 'Email deleted successfully.');
    }

    public function charityEmailStore(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'charity_id' => 'required'
        ]);

        $data = new UserDetail();
        $data->email = $request->email;
        $data->charity_id = $request->charity_id;
        $data->date = date('Y-m-d');
        $data->save();

        return response()->json([
            'status' => 200,
            'message' => 'Email added successfully!',
            'data' => $data
        ]);
    }

    public function charityEmailupdate(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'email' => 'required|email'
        ]);

        $data = UserDetail::find($request->id);
        $data->email = $request->email;
        $data->save();

        return response()->json([
            'status' => 200,
            'message' => 'Email updated successfully!',
            'data' => $data
        ]);
    }



}
