<?php

namespace App\Http\Resources;

use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'user_id' => $this->user_id,
            'nama' => $this->nama,
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->role,
            'status_akun' => $this->status_akun,
            'deskripsi' => $this->deskripsi,
            'jenis_kelamin' => $this->jenis_kelamin,

            // 'semester' => new SemesterResource($this->whenLoaded('semester')),
            'semester' => $this->whenLoaded('semester')?->nomor_semester,
            // 'major' => new MajorResource($this->whenLoaded('major')),
            'major' => $this->whenLoaded('major')?->nama_jurusan,
            // 'faculty' => new FacultyResource($this->whenLoaded('faculty')),
            'faculty' => $this->whenLoaded('faculty')?->nama_fakultas,

            'matkul_favorit' => $this->matkul_favorit,
            // 'foto_profil' => $this->foto_profil,
            'jumlah_like' => $this->jumlah_like,
            // 'rating' => $this->rating,

            'foto_profil' =>  url(asset('storage/' . $this->foto_profil)),

            // 'notes_jualan' => NoteResource::collection($this->whenLoaded('notes')),
            // 'notes_koleksi' => SavedNoteResource::collection($this->whenLoaded('savedNotes'))

            'created_at' => $this->created_at->toIso8601String()
        ];
    }
}
