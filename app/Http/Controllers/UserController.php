<?php

namespace App\Http\Controllers;

use App\Http\Resources\AllFollowingsCollection;
use App\Http\Resources\AllPostsCollection;
use App\Models\Following;
use App\Models\Post;
use App\Models\User;
use App\Services\FileService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // find user by id
        $user = User::find($id);
        if ($user === null) {
            return redirect()->route('home.index');
        }

        $posts = Post::where('user_id', $id)->orderBy('created_at', 'desc')->get();
        $followings = Following::where('user_id', $id)->orderBy('created_at', 'asc')->get();
        $followers = Following::where('following_user_id', $id)->orderBy('created_at', 'asc')->get();;
        $allFollowings = Following::orderBy('created_at', 'asc')->get();

        // return user together with all of their posts
        return Inertia::render('User', [
            'user' => $user,
            'postsByUser' => new AllPostsCollection($posts),
            'followingsByUser' => new AllFollowingsCollection($followings),
            'followersByUser' => new AllFollowingsCollection($followers),
            'allFollowings' => new AllFollowingsCollection($allFollowings)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate(['file' => 'required|mimes:jpg,jpeg,png']);
        $user = (new FileService)->updateFile(auth()->user(), $request, 'user');
        $user->save();

        return redirect()->route('users.show', ['id' => auth()->user()->id]);
    }
}
