<?php

namespace App\Observers;

use App\Topic;

class TopicObserver
{
    /**
     * Handle the topic "deleting" event.
     *
     * @param  \App\Topic  $topic
     * @return void
     */
    public function deleting(Topic $topic)
    {
        /**
         * Delete all questions from the topic
         */
        foreach($topic->questions as $question) {
            $question->delete();
        }
    }
}
