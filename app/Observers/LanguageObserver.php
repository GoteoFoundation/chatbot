<?php

namespace App\Observers;

use App\Language;

class LanguageObserver
{
    /**
     * Handle the language "deleting" event.
     *
     * @param  \App\Language  $language
     * @return void
     */
    public function deleting(Language $language)
    {
        $language->translations()->delete();

        /**
         * Delete all copies from the language
         */
        foreach($language->copies as $copy) {
            $copy->delete();
        }
    }
}
