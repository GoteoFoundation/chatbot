<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            LanguagesTableSeeder::class,
            CopiesTableSeeder::class,
        ]);

        if (!App::environment('production')) {
            $this->seedContent();
        }
    }

    private function seedContent()
    {
        $topicsTableSeeder = new TopicsTableSeeder();
        $topics = $topicsTableSeeder->run();

        $topics->each(function ($topic_id) {
            $questionsTableSeeder = new QuestionsTableSeeder();
            $questions = $questionsTableSeeder->run($topic_id);

            $questions->each(function ($question_id) use ($topic_id) {
                $answersTableSeeder = new AnswersTableSeeder();
                $answersTableSeeder->run($topic_id, $question_id);
            });
        });
    }
}
