<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Resources\AnswerResource;
use App\Http\Resources\QuestionResource;
use App\Http\Resources\SkillResource;
use App\Http\Resources\UserResource;
use App\Skill;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SkillController extends Controller
{
    public $successStatus = 200;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $skills = auth()->user()->skills;
        return response()->json(['success' => SkillResource::collection($skills)], $this->successStatus);
    }


    public function skillsWithId(User $user) {
        $skills = $user->skills;
        return response()->json(['success' => SkillResource::collection($skills)], $this->successStatus);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message'=>$validator->errors()], 401);
        }

        $user = auth()->user();
        $name = strtolower($request->name);

        // Trying to get Skill
        $skill = Skill::where('name', $name)->first();

        // If skill is not instance of Skill, creating and attaching
        if(!($skill instanceof Skill)) {
            $skill = new Skill();
            $skill->name = $name;
            $skill->save();
            $user->skills()->attach($skill);
            return response()->json(['success'=> new SkillResource($skill)], $this->successStatus);
        }
        // Checking for user already assigned to this skill or not
        else if(!Helper::hasUserSkill($skill)) {
            $user->skills()->attach($skill);
            return response()->json(['success'=> new SkillResource($skill)], $this->successStatus);
        }
        // Lastly, either error happened or skill already exist in user's skill set
        else {
            return response()->json(['message'=> 'problem with adding or skill already exist!'], 500);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Skill  $skill
     * @return \Illuminate\Http\Response
     */
    public function show(Skill $skill)
    {
        //
        $user = auth()->user();

        $questions = $skill->questions->where('user_id', $user->id);
        $user_skill_answers = Helper::findUserAnswersOnQuestions($user, $questions);

        return response()->json(['success'=> [
            'questions' => QuestionResource::collection($questions),
            'answers' => AnswerResource::collection($user_skill_answers),
        ]], $this->successStatus);
    }


    public function showWithId(Skill $skill, User $user) {
        $questions = $skill->questions->where('user_id', $user->id);
        $user_skill_answers = Helper::findUserAnswersOnQuestions($user, $skill->questions);

        return response()->json(['success'=> [
            'questions' => QuestionResource::collection($questions),
            'answers' => AnswerResource::collection($user_skill_answers),
        ]], $this->successStatus);
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Skill  $skill
     * @return \Illuminate\Http\Response
     */
    public function destroy(Skill $skill)
    {
        //
        auth()->user()->skills()->detach($skill);
        return response()->json(['success'=> 'Skill deleted successfully'], $this->successStatus);
    }

    public function general() {
        $user = auth()->user();
        $questions = $user->questions;
        $answers = $user->answers;

        return response()->json(['success'=> [
            'questions' => QuestionResource::collection($questions),
            'answers' => AnswerResource::collection($answers),
        ]], $this->successStatus);
    }

    public function generalWithId(User $user) {
        $questions = $user->questions;
        $answers = $user->answers;

        return response()->json(['success'=> [
            'questions' => QuestionResource::collection($questions),
            'answers' => AnswerResource::collection($answers),
        ]], $this->successStatus);
    }

    public function all(Request $request) {
        $term = trim($request->search);

        if (empty($term)) {
            return \Response::json([]);
        }

        return response()->json([
            'success' => SkillResource::collection(
                Skill::where('name', 'like', $term . '%')->limit(10)->get()
            )
        ], $this->successStatus);
    }
}
