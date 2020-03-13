<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Question;
use App\Answer;
use App\Topic;
use App\Language;
use App\Observers\QuestionObserver;
use App\Observers\AnswerObserver;
use App\Observers\TopicObserver;
use App\Observers\LanguageObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Question::observe(QuestionObserver::class);
        Answer::observe(AnswerObserver::class);
        Topic::observe(TopicObserver::class);
        Language::observe(LanguageObserver::class);
    }
}
