<?php

namespace App\Observers;

use App\Answer;

class AnswerObserver
{
    /**
     * Handle the answer "deleting" event.
     *
     * @param  \App\Answer  $answer
     * @return void
     */
    public function deleting(Answer $answer)
    {
        $answer->translations()->delete();
        $answer->translationsUrl()->delete();
    }
}
