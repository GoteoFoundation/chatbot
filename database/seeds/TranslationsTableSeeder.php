<?php

use App\Translation;
use App\Traits\LanguageTrait;
use Illuminate\Database\Seeder;

class TranslationsTableSeeder extends Seeder
{
    use LanguageTrait;

    /**
     * Run the database seeds.
     *
     * @return integer
     */
    public function run($isUrl = false)
    {
        $languages = $this->getLanguages();
        $translation_id = Translation::max('translation_id') + 1;

        $languages->map(function ($language) use ($translation_id, $isUrl) {
            if($language->is_parent || rand(0, 2) < 2) {
                factory(App\Translation::class)->create([
                    'language_id' => $language->id,
                    'translation_id' => $translation_id,
                    'term' => function (&$data) use ($language, $isUrl) {
                        if($isUrl) {
                            $term = $data['url_raw'];
                        } else {
                            $term = $data['term_raw'] . ' - ' . strtoupper($language->iso_code);
                        }
                        unset($data['term_raw'], $data['url_raw']);
                        return $term;
                    }
                ]);
            }
        });

        return $translation_id;
    }
}
