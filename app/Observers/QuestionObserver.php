<?php

namespace App\Observers;

use App\Answer;
use App\Question;

class QuestionObserver
{
    /**
     * Handle the question "deleting" event.
     *
     * @param  \App\Question  $question
     * @return void
     */
    public function deleting(Question $question)
    {
        /**
         * Delete all answers from the question
         */
        foreach($question->answers as $answer) {
            $answer->delete();
        }
        $question->translations()->delete();

        /**
         * Remove this question as answer question
         */
        $answers = Answer::where([
            'answer_question_id' => $question->id,
        ])->get();

        foreach($answers as $answer) {
            $answer->answer_question_id = null;
            $answer->save();
        }
    }
}
