<?php

use Illuminate\Database\Seeder;

class QuestionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return \Illuminate\Support\Collection
     */
    public function run($topic_id = null)
    {
        $is_parent = true;
        return factory(App\Question::class, rand(3, 10))->create([
            'question_i18n' => function () {
                $translationsSeeder = new TranslationsTableSeeder();
                return $translationsSeeder->run();
            },
            'topic_id' => $topic_id,
            'is_parent' => function () use (&$is_parent) {
                if($is_parent) {
                    $is_parent = false;
                    return true;
                } else {
                    return $is_parent;
                }
            },
        ])->pluck('id');
    }
}
