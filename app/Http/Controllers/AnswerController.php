<?php

namespace App\Http\Controllers;

use App\Answer;
use App\Helpers\Helper;
use App\Http\Resources\AnswerResource;
use App\Http\Resources\QuestionResource;
use App\Notifications\QuestionAnswered;
use App\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class AnswerController extends Controller
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
        //
        $validator = Validator::make($request->all(), [
            'content' => 'required',
            'question_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message'=> implode(',', $validator->errors()->all())], 401);
        }

        $user = auth()->user();
        $request['user_id'] = $user->id;

        // Adding Answer
        $answer = Answer::create($request->all());

        // Sending Notification to Question Owner (Except if answered user is question owner)
        // 1. Finding Question User
        $question = Question::find($request['question_id']);
        $questionUser = $question->user;
        // 2. Checking Auth User and Question User for not being equal
        if($questionUser->id != $user->id) {
            // 3. Sending Notification
            $questionUser->notify(new QuestionAnswered($answer, $question));
        }

        return response()->json(['success'=> new AnswerResource($answer)], $this->successStatus);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Answer  $answer
     * @return \Illuminate\Http\Response
     */
    public function show(Answer $answer) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Answer  $answer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Answer $answer)
    {
        // Authorization Check
        if (Gate::denies('update-delete-answer', $answer)) {
            return response()->json(['message'=> 'Cannot edit answer'], 401);
        }

        //
        $validator = Validator::make($request->all(), [
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message'=> implode(',', $validator->errors()->all())], 400);
        }

        $answer = $answer->update([
            'content' => $request['content']
        ]);

        return response()->json(['success'=> $answer], $this->successStatus);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Answer  $answer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Answer $answer)
    {
        // Authorization Check
        if (Gate::denies('update-delete-answer', $answer)) {
            return response()->json(['message' => 'Cannot delete answer'], 400);
        }

        // Deleting Answer
        $status = $answer->delete();
        return response()->json(['success'=> $status], $this->successStatus);
    }

    public function questionAnswers(Question $question, Request $request) {
        $answers = $question->answers->sortByDesc('voteSum')/*->paginate(5)*/;



//        return Helper::paginateCollection($answers, 5);;

        return AnswerResource::collection(Helper::paginateCollection($answers, 5));

//        return response()->json(['success'=> AnswerResource::collection($answers)], $this->successStatus);
    }

    public function up(Answer $answer) {
        // Checking if have voted already or not
        $user = auth()->user();

        $vote = $answer->votes->where('user_id', $user->id)->first();

        // if not => adding up vote (with value 1)
        if(empty($vote)) {
            $answer->voteUsers()->save($user, ['value' => 1]);
        }
        // if yes => checking values
        else {
            $value = $vote->value;
            if($value == 0 || $value == -1) $vote->value = 1;
            if($value == 1) $vote->value = 0;
            $vote->save();
        }


        // Returning answer as a result. (Answer's Votes will be needed in Front - Vue.js)
        return response()->json(['success'=> new AnswerResource($answer->refresh())], $this->successStatus);
    }

    public function down(Answer $answer) {
        // Checking if have voted already or not
        $user = auth()->user();

        $vote = $answer->votes->where('user_id', $user->id)->first();

        // if not => adding up vote (with value -1)
        if(empty($vote)) {
            $answer->voteUsers()->save($user, ['value' => -1]);
        }
        // if yes => checking values
        else {
            $value = $vote->value;
            if($value == 0 || $value == 1) $vote->value = -1;
            if($value == -1) $vote->value = 0;
            $vote->save();
        }


        // Returning answer as a result. (Answer's Votes will be needed in Front - Vue.js)
        return response()->json(['success'=> new AnswerResource($answer->refresh())], $this->successStatus);
    }

    public function best(Answer $answer) {
        // Authorization: Only Question's User can Choose Best Answer
        if (Gate::denies('make-answer-best', $answer)) {
            return response()->json(['message'=> 'Cannot make answer best'], 401);
        }

        // Saving current status for later use to make best or cancel
        $status = $answer->is_best;

        // Finding and making all Answers' best_answer filed 0
        $question = $answer->question;
        foreach ($question->answers as $q_answer) {
            $q_answer->is_best = 0;
            $q_answer->save();
        }

        // Making current answer best if initial best value 0
        if($status == 0) {
            $answer->is_best = 1;
            $answer->save();
        }


        // Returning answer as a result. (Answer's Votes will be needed in Front - Vue.js)
        return response()->json(['success'=> new AnswerResource($answer->refresh())], $this->successStatus);
    }
}
