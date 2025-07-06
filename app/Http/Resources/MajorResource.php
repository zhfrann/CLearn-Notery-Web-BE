<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MajorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'major_id' => $this->major_id,
            'nama_jurusan' => $this->nama_jurusan,
            'kode_jurusan' => $this->kode_jurusan,
            'faculty' => new FacultyResource($this->whenLoaded('faculty'))
        ];
    }
}
