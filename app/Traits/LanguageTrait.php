<?php

namespace App\Traits;

use App\Language;
use App\Translation;

trait LanguageTrait
{
    public function getAvailableCopies()
    {
        return collect([
            'icon_tooltip' => __('Icon tooltip'),
            'widget_title' => __('Widget title'),
            'widget_welcome' => __('Widget welcome'),
            'start_icon' => __('Start button label'),
            'close_icon' => __('Close icon alternative label'),
            'go_back_icon' => __('Go back icon alternative label'),
            'reset_icon' => __('Restart icon alternative label'),
            'faq_title' => __('FAQ title'),
            'faq_url' => __('FAQ url'),
            'loading' => __('Loading alternative text'),
            'error_title' => __('Error title'),
            'error_message' => __('Error message'),
            'error_retry' => __('Retry button label'),
        ]);
    }

    public function getLanguages()
    {
        return Language::orderByDesc('is_parent')->get();
    }

    public function getParentLanguage()
    {
        return Language::where(['is_parent' => 1])->firstOrFail();
    }

    public function getParentLanguageIsoCode()
    {
        return $this->getParentLanguage()->iso_code;
    }

    public function getParentLanguageId()
    {
        return $this->getParentLanguage()->id;
    }

    public function getLanguageByIsoCode($iso_code)
    {
        return Language::where(['iso_code' => $iso_code])->firstOrFail();
    }

    public function createTranslation($key, $value, $translation_id = null)
    {
        if (is_numeric($key)) {
            $language = Language::find($key);
        } else {
            $language = Language::where(['iso_code' => $key])->firstOrFail();
        }

        $id = $translation_id ?? (Translation::max('translation_id') + 1);

        return new Translation([
            'translation_id' => $id,
            'language_id' => $language->id,
            'term' => $value,
        ]);
    }

    public function createTranslationTerms(&$objects, $objectLanguageById = false)
    {
        $parent = $objectLanguageById ? $this->getParentLanguageId() : $this->getParentLanguageIsoCode();

        if (isset($objects[$parent])) {
            /**
             * Create parent language translation
             */
            $translation = $this->createTranslation(
                $parent,
                $objects[$parent]
            );
            $translation->save();

            /**
             * Unset it from objects
             */
            unset($objects[$parent]);

            /**
             * And create the other ones based on parent translation
             */
            foreach ($objects as $code => $name) {
                if (!empty($name)) {
                    $this->createTranslation(
                        $code,
                        $name,
                        $translation->translation_id
                    )->save();
                }
            }

            return $translation->translation_id;
        }
    }

    public function updateTranslationTerms($parent, &$objects)
    {
        /**
         * Update those existing translations
         */
        $translationId = null;
        $parent->map(function ($translation) use (&$objects, &$translationId) {
            $translation->setTerm($objects[$translation->language_id]);
            unset($objects[$translation->language_id]);
            $translationId = $translation->translation_id;
            $translation->save();
        });

        /**
         * Bail early if updating and nothing is found
         */
        if (is_null($translationId)) {
            return false;
        }

        /**
         * Create new translations
         */
        if (is_array($objects)) {
            foreach ($objects as $languageId => $newTranslation) {
                if (!empty($newTranslation) && !is_null($translationId)) {
                    $this->createTranslation(
                        $languageId,
                        $newTranslation,
                        $translationId
                    )->save();
                }
            }
        }

        return true;
    }

    public function getRealTranslation($languageId, $relation)
    {
        return optional($relation->first(function ($translation) use ($languageId) {
            return is_null($languageId) ? $translation->isMainTranslation() : $translation->belongedByLanguage($languageId);
        }))->getTerm();
    }

    public function getFinalTranslation($languageId, $relation)
    {
        $term = optional($relation->first(function ($translation) use ($languageId) {
            return is_null($languageId) ? $translation->isMainTranslation() : $translation->belongedByLanguage($languageId);
        }))->getTerm();

        if ($term) {
            return $term;
        }

        if (! ($language = Language::find($languageId))) {
            return null;
        }

        $term = optional($relation->first(function ($translation) use ($language) {
            return $translation->belongedByLanguage(optional($language->supportLanguage)->id);
        }))->getTerm();

        if ($term) {
            return $term;
        } else {
            return optional($relation->first(function ($translation) {
                return $translation->isMainTranslation();
            }))->getTerm();
        }
    }
}
