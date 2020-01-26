<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Resources\QuestionCollection;
use App\Http\Resources\QuestionResource;
use App\Notifications\SkillRelatedQuestionAdded;
use App\Question;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class QuestionController extends Controller
{
    public $successStatus = 200;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        return $request->all();
        //
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required',
            'language_id' => 'required',
            'skills' => 'required|array',
            'skills.*' => 'integer',
        ]);


        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }

        $user = auth()->user();

        $request['user_id'] = $user->id;

        // Creating Question
        $question = Question::create([
            'title' => $request['title'],
            'content' => $request['content'],
            'user_id' => $user->id,
            'language_id' => isset($request->language_id) ? intval($request->language_id) : null
        ]);

        // SYNC Skills to Question
        $question->skills()->sync($request->skills);

        // Sending Notifications to Skill(and Language) Related Users
        $skillRelatedUsers = Helper::getSkillRelatedUsers($question);
        Notification::send($skillRelatedUsers, new SkillRelatedQuestionAdded($question));

        return response()->json(['success'=> new QuestionResource($question)], $this->successStatus);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function show(Question $question)
    {
        // Checking for currently auth user has notification about this question, if has markAsRead that notification
        if(isset(auth()->user()->unreadNotifications)) {
            foreach (auth()->user()->unreadNotifications as $notification) {
                if($notification->data['question_id'] == $question->id) {
                    $notification->markAsRead();
                }
            }
        }

        return response()->json(['success'=> new QuestionResource($question)], $this->successStatus);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Question $question)
    {
        // Authorization Check
        if (Gate::denies('update-delete-question', $question)) {
            return response()->json(['message'=> 'Cannot edit question'], 401);
        }

        //
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required',
            'language_id' => 'required',
            'skills' => 'required|array',
            'skills.*' => 'integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => implode(",",$validator->errors()->all())], 401);
        }

        $user = auth()->user();

        $request['user_id'] = $user->id;

        // Updating Skills of Question
        $question->skills()->sync($request->skills);

        // Updating Question
        $question->update([
            'title' => $request['title'],
            'content' => $request['content'],
            'language_id' => $request['language_id'],
        ]);


        return response()->json(['success'=> new QuestionResource($question)], $this->successStatus);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function destroy(Question $question)
    {
        // Authorization Check
        if (Gate::denies('update-delete-question', $question)) {
            return response()->json(['message'=> 'Cannot delete question'], 400);
        }

        // Deleting Question
        $status = $question->delete();
        return response()->json(['success'=> $status], $this->successStatus);
    }

    public function recent() {
        // Getting Language and Skill Related Questions
        // 1. Getting Auth User's Skill IDs
        $user_skill_ids = auth()->user()->skills->pluck('id')->toArray();

        // 2. Querying Question for Skills and Language
        $relatedQuestions = Question::whereHas('skills', function(Builder $query) use ($user_skill_ids) {
            $query->whereIn('skills.id', $user_skill_ids);
        })->whereIn('language_id', auth()->user()->languages->pluck('id')->toArray())
            ->orderBy('created_at', 'desc')->paginate(10);

        return QuestionResource::collection($relatedQuestions);
    }

    public function search($key) {
        $term = trim($key);

        if (empty($term)) {
            return \Response::json([]);
        }

        $questions = Question::where('title', 'like', '%' . $term . '%')
            ->orWhere('content', 'like', '%' . $term . '%')->paginate(20);

        return QuestionResource::collection($questions);
    }
}
