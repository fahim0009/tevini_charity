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

}
