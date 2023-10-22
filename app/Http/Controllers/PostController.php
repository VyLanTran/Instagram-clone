<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Services\FileService;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $post = new Post;
        $request->validate([
            'file' => 'required|mimes:jpg,jpeg,png,mp4',
            'caption' => 'nullable',
            'location' => 'nullable',
        ]);

        if ($request->input('isVideo')) {
            $post = (new FileService)->addVideo($post, $request);
        } else {

            $post = (new FileService)->updateFile($post, $request, 'post');
        }

        $post->user_id = auth()->user()->id;
        $post->is_video = $request->input('isVideo');
        $post->caption = $request->input('caption', '');
        $post->location = $request->input('location', '');
        $post->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $post = Post::find($id);

        if (!empty($post->file)) {
            $currentFile = public_path() . $post->file;

            if (file_exists($currentFile)) {
                unlink($currentFile);
            }
        }

        $post->delete();
    }
}
