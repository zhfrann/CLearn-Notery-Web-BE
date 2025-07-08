<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function getAllNotes(Request $request) {}

    public function latestNotes(Request $request) {}

    public function mostLikeNotes(Request $request) {}

    public function topCreator(Request $request) {}

    public function createNote(Request $request) {}

    public function getNoteDetail(Request $request, string $id) {}

    public function addLikeNote(Request $request, string $id) {}

    public function addFavoriteNote(Request $request, string $id) {}

    public function updateNote(Request $request, string $id) {}

    public function deleteNote(Request $request, string $id) {}

    // public function buyNote(Request $request, string $id) {}
}
