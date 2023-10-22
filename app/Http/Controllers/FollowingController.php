<?php

namespace App\Http\Controllers;

use App\Models\Following;
use Illuminate\Http\Request;

class FollowingController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $request->validate(['user_id' => 'required']);
        $following = new Following();

        $following->user_id = auth()->user()->id;
        $following->following_user_id = $request->input('following_user_id');
        $following->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $following = Following::find($id);
        $following->delete();
    }
}
