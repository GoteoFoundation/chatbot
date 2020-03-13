<?php

use Illuminate\Database\Seeder;

class TopicsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return \Illuminate\Support\Collection
     */
    public function run()
    {
        return factory(App\Topic::class, 3)->create()->pluck('id');
    }
}
