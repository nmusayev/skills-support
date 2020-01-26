<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Resources\LanguageResource;
use App\Http\Resources\UserResource;
use App\Language;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Input\Input;
use Validator;

class UserController extends Controller
{
    public $successStatus = 200;

    public function login(){
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
            $user = Auth::user();
            $success['token'] =  $user->createToken('MyApp')-> accessToken;
            return response()->json(['success' => $success], $this-> successStatus);
        }
        else{
            return response()->json(['error'=>'Unauthorised'], 401);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
            'languages' => 'required|array',
            'languages.*' => 'integer',
        ]);

        // Validation Check
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }

        // Creating User
        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => bcrypt($request['password']),
        ]);

        // Assigning Languages to Newly Created User
        $user->languages()->sync($request['languages']);

        // Creating token and returning
        $success['token'] =  $user->createToken('MyApp')-> accessToken;
        $success['name'] =  $user->name;
        return response()->json(['success'=>$success], $this->successStatus);
    }

    public function details()
    {
        $user = Auth::user();
        return response()->json(['success' => new UserResource($user)], $this->successStatus);
    }

    public function detailsWithId(User $user) {
        return response()->json(['success' => new UserResource($user)], $this->successStatus);
    }

    public function update(Request $request) {
//        $validator = Validator::make($request->all(), [
//            'name' => 'required',
//            'email' => 'required|email',
//        ]);

//        if ($validator->fails()) {
//            return response()->json(['message'=>$validator->errors()], 400);
//        }

        $user = auth()->user();
        $user->update([
            'name' => isset($request->name) && !empty($request->name) ? $request->name : $user->name,
            'email' => isset($request->email) && !empty($request->email) ? $request->email : $user->email,
            'linkedin_profile' => isset($request->linkedin_profile) && !empty($request->linkedin_profile)
                ? $request->linkedin_profile : $user->linkedin_profile,
        ]);

        return response()->json(['success'=> new UserResource($user)], $this->successStatus);
    }

    public function updateProfileImage(Request $request) {
        $this->validate($request, [
           'image' => 'file'
        ]);

        if ($request->hasFile('image')) {
            $image = $request['image'];
            $fileName = time() . $image->getClientOriginalName();

            $path = 'user/profile/' . $fileName;
            Storage::disk('public')->put($path, file_get_contents($image));

            // Getting auth user and uploading profile image
            $user = auth()->user();

            // 1. Deleting current picture from storage
            // 1.1 Check if file exist
            $profile_image = $user->profile_image;
            if(Storage::disk('public')->exists($profile_image)
                && $profile_image !== 'user/profile/default.jpg') {
                // 1.2 Deleting file
                Storage::disk('public')->delete($profile_image);
            }

            // 2. Updating profile picture
            $user->profile_image = $path;
            $user->save();

            return storage_path($user->profile_image);
        }
    }

    public function addLanguage(Request $request) {
        $this->validate($request, [
            'id' => 'required'
        ]);


        // Finding Language
        $id = $request['id'];
        $language = Language::find($id);

        if(isset($language) and !Helper::checkUserHasLanguage($language)) {
            auth()->user()->languages()->syncWithoutDetaching($language);

            return response()->json(['success'=> new LanguageResource($language)], $this->successStatus);
        } else {
            return response()->json(['message'=> 'Added Language did not find or already exist!'], 404);
        }
    }

    public function deleteLanguage(Request $request) {
        $this->validate($request, [
            'id' => 'required'
        ]);

        // Finding Language
        $id = $request['id'];
        $language = Language::find($id);

        if(isset($language) and Helper::checkUserHasLanguage($language)) {
            auth()->user()->languages()->detach($language);

            return response()->json(['success'=> 'Language successfully detached from user'], $this->successStatus);
        } else {
            return response()->json(['message'=> 'Language cannot be detached!'], 404);
        }
    }

    public function all(Request $request) {
        $name = isset($request['name']) ? $request['name'] : null;

        if(is_null($name) || !isset($name) || empty($name)) {
            $users = User::all()->sortByDesc('overallPoint');

            return UserResource::collection(Helper::paginateCollection($users, 15));
        } else {
            if (empty($name)) {
                return \Response::json([]);
            }

            $users = User::where('name', 'like', '%' . $name . '%')->paginate(15)/*->sortByDesc('overallPoint')*/;

            return UserResource::collection($users);
        }
    }

    public function search() {


    }
}
