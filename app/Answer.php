<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LanguageTrait;

class Answer extends Model
{
    use LanguageTrait;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['id', 'answer_i18n', 'order', 'parent_question_id', 'answer_question_id', 'url_i18n', 'created_at', 'updated_at'];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['answer', 'type', 'value'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['answer', 'type', 'value'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['answer_i18n', 'order', 'parent_question_id', 'answer_question_id', 'url_i18n'];

    /**
     * Already done questions when converting to array to prevent
     * endless loop recursion
     *
     * @var array
     */
    protected $doneQuestionIds;

    /**
     * Get the answer type.
     *
     * @return string
     */
    public function getTypeAttribute()
    {
        return is_null($this->question) ? 'url' : 'question';
    }

    /**
     * Check if answer contains URL.
     */
    public function isUrlAnswer()
    {
        return $this->type == 'url';
    }

    /**
     * Check if answer contains Question.
     */
    public function isQuestionAnswer()
    {
        return $this->type == 'question';
    }

    /**
     * Get the translated name.
     *
     * @return bool
     */
    public function getNameAttribute()
    {
        return $this->trans();
    }

    /**
     * Get the translated URL.
     *
     * @return bool
     */
    public function getUrlAttribute($languageId = null, $real = false)
    {
        return $real
            ? $this->getRealTranslation($languageId, $this->translationsUrl)
            : $this->getFinalTranslation($languageId, $this->translationsUrl);
    }

    /**
     * Get the children of the question.
     */
    public function getChildrenAttribute()
    {
        return $this->isUrlAnswer() ? [] : [optional($this->question)->toTree($this->doneQuestionIds)];
    }

    /**
     * Get the text of the question.
     */
    public function getTextAttribute()
    {
        return view('questions.jstree.answer.text', ['answer' => $this])->render();
    }

    /**
     * Get the icon of the answer.
     */
    public function getIconAttribute()
    {
        return view('questions.jstree.answer.icon')->render();
    }

    /**
     * Get the answer.
     *
     * @return string
     */
    public function getAnswerAttribute()
    {
        return $this->name;
    }

    /**
     * Get the value based on type.
     *
     * @return string
     */
    public function getValueAttribute()
    {
        if($this->isUrlAnswer()) {
            $value =  $this->url;
        } elseif ($this->isQuestionAnswer()) {
            $value =  $this->answer_question_id;
        } else {
            $value = '';
        }

        return $value;
    }

    /**
     * Returns true if the answer is incompleted.
     *
     * @return boolean
     */
    public function isIncomplete()
    {
        return (is_null($this->question) && is_null($this->url))
            || ($this->isQuestionAnswer() && $this->question->isIncomplete());
    }

    /**
     * Check if answer matches a given term.
     *
     * @return boolean
     */
    public function hasTerm($term)
    {
        return strpos($this->trans(), $term) !== false;
    }

    /**
     * Returns true if the answer has pending languages.
     *
     * @return boolean
     */
    public function hasPendingLanguage($countLanguages)
    {
        return $this->translations->count() < $countLanguages
            || ($this->isUrlAnswer() && $this->translationsUrl->count() < $countLanguages);
    }

    /**
     * Get all reachable questions from answer.
     *
     * @param $allReachableQuestionsIds
     */
    public function getAllReachableQuestionsIds(&$allReachableQuestionsIds)
    {
        if($this->isQuestionAnswer() && !in_array(optional($this->question)->id, $allReachableQuestionsIds)) {
            $allReachableQuestionsIds[] = optional($this->question)->id;
            optional($this->question)->getAllReachableQuestionsIds($allReachableQuestionsIds);
        }
    }

    /**
     * Converts answer to array for tree.
     */
    public function toTree($doneQuestionIds)
    {
        $this->doneQuestionIds = $doneQuestionIds;

        return $this->setAppends(['text', 'children', 'icon'])
            ->makeVisible(['text', 'children', 'icon'])
            ->toArray();
    }

    /**
     * Converts answer to array by language.
     */
    public function toArrayByLanguage($languageId)
    {
        if($this->isUrlAnswer()) {
            $value = $this->getUrlAttribute($languageId);
        }

        return [
            'answer' => $this->trans($languageId),
            'type' => $this->type,
            'value' => $value ?? $this->value,
        ];
    }

    /**
     * Get the parent question to this answer.
     */
    public function parent()
    {
        return $this->belongsTo('App\Question', 'parent_question_id', 'id');
    }

    /**
     * Get the related question to this answer.
     */
    public function question()
    {
        return $this->belongsTo('App\Question', 'answer_question_id', 'id');
    }

    /**
     * Get all the answer's text translations.
     */
    public function translations()
    {
        return $this->hasMany('App\Translation', 'translation_id', 'answer_i18n');
    }

    /**
     * Get all the answer's URL translations.
     */
    public function translationsUrl()
    {
        return $this->hasMany('App\Translation', 'translation_id', 'url_i18n');
    }

    /**
     * Get the translation by language or the main translation.
     */
    public function trans($languageId = null, $real = false)
    {
        return $real
            ? $this->getRealTranslation($languageId, $this->translations)
            : $this->getFinalTranslation($languageId, $this->translations);
    }
}
