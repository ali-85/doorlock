<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class captureDoorlockResorce extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "status" => "success",
            "ket" => "Capture Doorlock Berhasil di tambahkan",
            "data" => [
                'id' => $this->id,
                'uid' => $this->uid,
                'user_id' => $this->user_id,
                'links' => [
                    'self' => URL::to('/') .'/'. $this->doorlock_photo_path
                ],
            ]
        ];
    }
}
