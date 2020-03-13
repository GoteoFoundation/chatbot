<?php

use Illuminate\Database\Seeder;

class LanguagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $languages = [
            [
                'name' => 'Español',
                'iso_code' => 'es',
                'is_parent' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Català',
                'iso_code' => 'ca',
                'is_parent' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'English',
                'iso_code' => 'en',
                'is_parent' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach($languages as $language) {
            DB::table('languages')->insert($language);
        }
    }
}
