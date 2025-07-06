<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
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
            'course_id' => $this->course_id,
            'nama_mk' => $this->nama_mk,
            'major' => new MajorResource($this->whenLoaded('major')),
            'semester' => new SemesterResource($this->whenLoaded('semester'))
        ];
    }
}
