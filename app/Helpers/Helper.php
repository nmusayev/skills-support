<?php


namespace App\Helpers;


use App\Answer;
use App\Language;
use App\Skill;
use App\User;
use Illuminate\Support\Collection;

class Helper
{
    public static function skillExist($name)
    {
        $skill = Skill::where('name', $name)->first();
        return $skill instanceof Skill;
    }

    public static function hasUserSkill($skill)
    {
        $user_skill_ids = auth()->user()->skills->pluck('id')->toArray();
        $skill_id = $skill->id;

        return in_array($skill_id, $user_skill_ids);
    }

    public static function findUserAnswersOnQuestions($user, $questions)
    {
        $skill_related_answer_ids = [];

        foreach ($questions as $question) {
            $skill_related_answer_ids =
                array_merge($skill_related_answer_ids,
                    $question->answers->where('user_id', $user->id)->pluck('id')->toArray());
        }


        return Answer::whereIn('id', $skill_related_answer_ids)->get();
    }

    public static function checkUserHasLanguage($language)
    {
        $user_language_ids = auth()->user()->languages->pluck('id')->toArray();

        return in_array($language->id, $user_language_ids);
    }


    public static function paginateCollection($items, $perPage = 15, $page = null, $options = []) {
        $page = $page ?: (\Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof \Illuminate\Support\Collection ? $items : \Illuminate\Support\Collection::make($items);
        return new \Illuminate\Pagination\LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    public static function getSkillRelatedUsers($question) {
        // Getting Language Related Users
        $users = Language::find($question->language_id)->users->where('id', '!=', auth()->user()->id);

        // Filter Users collection to gel only question skills sufficient users
        return $users->filter(function($user) use ($question) {
            $question_skill_ids = $question->skills->pluck('id')->toArray();
            $user_skill_ids = $user->skills->pluck('id')->toArray();

            // exactly equal skill in person
//            return  empty(array_diff($question_skill_ids, $user_skill_ids));

            // For now at least one skill related to added question
            return count(array_diff($question_skill_ids, $user_skill_ids)) < count($question_skill_ids);
        });
    }
}
