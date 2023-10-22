<?php

namespace App\Services;

use Image;

class FileService
{
    public function updateFile($model, $request, $type)
    {
        if (!empty($model->file)) {
            $currentFile = public_path() . $model->file;

            if (file_exists($currentFile) && $currentFile != public_path() . '/user-placeholder.png') {
                unlink($currentFile);
            }
        }

        $file = null;
        if ($type === "user") {
            $file = Image::make($request->file('file'))->resize(400, 400);
        } else {
            $file = Image::make($request->file('file'));
        }
        $ext = $request->file('file');
        $extension = $ext->getClientOriginalExtension();
        $name = time() . '.' . $extension;
        $file->save(public_path() . '/file/' . $name);
        $model->file = '/file/' . $name;

        return $model;



        // if ($request->hasFile('file')) {
        //     $file = $request->file('file');

        //     // Determine the allowed file extensions based on the $type
        //     $allowedExtensions = $type === "user" ? ['jpg', 'jpeg', 'png'] : ['jpg', 'jpeg', 'png', 'mp4'];

        //     // Validate the file extension
        //     $extension = $file->getClientOriginalExtension();
        //     if (!in_array($extension, $allowedExtensions)) {
        //         // Handle invalid file extension here
        //         // You can return an error or throw an exception as needed
        //     }

        //     // Generate a unique filename
        //     $name = time() . '.' . $extension;
        //     $path = '/file/' . $name;

        //     if ($type === "user") {
        //         // Resize image for the user
        //         $image = Image::make($file)->resize(400, 400);
        //         $image->save(public_path() . $path);
        //     } else {
        //         // Move the file for posts
        //         $file->move(public_path() . '/file/', $name);
        //     }

        //     // Delete the old file, if it exists
        //     if (!empty($model->file) && $model->file != '/user-placeholder.png') {
        //         $currentFile = public_path() . $model->file;
        //         if (file_exists($currentFile)) {
        //             unlink($currentFile);
        //         }
        //     }

        //     // Update the model's file attribute
        //     $model->file = $path;
        // }

        // return $model;
    }

    public function addVideo($model, $request)
    {
        $video = $request->file('file');
        $extension = $video->getClientOriginalExtension();
        $name = time() . '.' . $extension;
        $video->move(public_path() . '/files/', $name);
        $model->file = '/files/' . $name;

        return $model;
    }
}
