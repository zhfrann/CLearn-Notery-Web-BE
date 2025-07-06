<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FacultyResource extends JsonResource
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
            'faculty_id' => $this->faculty_id,
            'nama_fakultas' => $this->nama_fakultas,
            'kode_fakultas' => $this->kode_fakultas,
        ];
    }
}
