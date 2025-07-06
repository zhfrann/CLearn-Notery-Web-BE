<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SavedNoteResource extends JsonResource
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
            'saved_note_id' => $this->saved_note_id,

            'note_id' => $this->note_id,
            // 'note' => new NoteResource($this->whenLoaded('note')),

            'user_id' => $this->user_id,
        ];
    }
}
