<?php

use Illuminate\Database\Seeder;

class AnswersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return \Illuminate\Support\Collection
     */
    public function run($topic_id = null, $parent_question_id = null)
    {
        $order = 1;
        return factory(App\Answer::class, rand(2, 5))->create([
            'answer_i18n' => function () {
                $translationsSeeder = new TranslationsTableSeeder();
                return $translationsSeeder->run();
            },
            'order' => function () use (&$order) {
                return $order++;
            },
            'parent_question_id' => $parent_question_id,
            'answer_question_id' => null,
            'url_i18n' => null,
        ])->each(function ($answer) use ($topic_id, $parent_question_id) {
            if(rand(0, 1) == 0) {
                $answer->answer_question_id = \App\Question::where([
                    ['topic_id', '=', $topic_id],
                    ['id', '!=', $parent_question_id],
                ])->get()->random(1)->first()->id;
            } else {
                $translationsSeeder = new TranslationsTableSeeder();
                $answer->url_i18n = $translationsSeeder->run(true);
            }
            $answer->save();
        })->pluck('id');
    }
}
