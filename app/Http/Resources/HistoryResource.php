<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HistoryResource extends JsonResource
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
            "ket" => "Counter Berhasil di tambahkan",
            "data" => [
                'id' => $this->id,
                'uid' => $this->uid,
                'user_id' => $this->user_id,
                'count_access' => $this->count_access,
            ]
        ];
    }
}
