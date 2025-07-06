<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SemesterResource extends JsonResource
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
            'semester_id' => $this->semester_id,
            'nomor_semester' => $this->nomor_semester,
            'tahun_ajaran' => $this->tahun_ajaran,
            'periode' => $this->periode,
            'major' => new MajorResource($this->whenLoaded('major'))
        ];
    }
}
