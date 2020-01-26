<?php

namespace App\Http\Controllers;

use App\Http\Resources\LanguageResource;
use App\Language;
use App\User;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    //
    public $successStatus = 200;

    public function index() {
        return response()->json(['success' => LanguageResource::collection(auth()->user()->languages)], $this->successStatus);
    }

    public function languagesWithId(User $user) {
        return response()->json(['success' => LanguageResource::collection($user->languages)], $this->successStatus);
    }

    public function all() {
        return response()->json(['success' => LanguageResource::collection(Language::all())], $this->successStatus);
    }
}
