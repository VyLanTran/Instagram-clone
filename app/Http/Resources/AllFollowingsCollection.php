<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AllFollowingsCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request)
    {
        return $this->collection->map(function ($following) {
            return [
                'id' => $following->id,
                'user' => [
                    'id' => $following->user->id,
                    'name' => $following->user->name,
                    'file' => $following->user->file
                ],
                'following_user' => [
                    'id' => $following->followingUser->id,
                    'name' => $following->followingUser->name,
                    'file' => $following->followingUser->file
                ]
            ];
        });
    }
}
