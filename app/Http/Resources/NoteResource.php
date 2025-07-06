<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NoteResource extends JsonResource
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
            'seller_id' => $this->seller_id,
            'course_id' => $this->course_id,
            'judul' => $this->judul,
            'deskripsi' => $this->deskripsi,
            'harga' => $this->harga,
            // 'rating' => $this->whenLoaded('reviews', fn() => round($this->reviews->avg('rating') ?? 0.0, 2)),
            // tags => ...
            'nama_file' => $this->nama_file,
            'jumlah_terjual' => $this->jumlah_terjual,
            'jumlah_like' => $this->jumlah_like,
            'jumlah_dikunjungi' => $this->jumlah_dikunjungi,
            'gambar_preview' => $this->gambar_preview,
        ];
    }
}
