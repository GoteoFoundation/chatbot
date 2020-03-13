<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LanguageTrait;

class Question extends Model
{
    use LanguageTrait;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['question_i18n', 'topic_id', 'is_parent', 'created_at', 'updated_at'];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['id', 'question'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['question'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['question_i18n', 'topic_id', 'is_parent'];

    /**
     * The text presentation
     *
     * @var boolean
     */
    protected $simpleText = false;

    /**
     * The question presentation
     *
     * @var boolean
     */
    protected $markParent = false;

    /**
     * Already done questions when converting to array to prevent
     * endless loop recursion
     *
     * @var array
     */
    protected $doneQuestionIds;

    /**
     * Get the children of the question.
     */
    public function getChildrenAttribute()
    {
        return $this->answers->map(function ($answer) {
            return $answer->toTree($this->doneQuestionIds);
        });
    }

    /**
     * Get the text of the question.
     */
    public function getTextAttribute()
    {
        return $this->simpleText ? $this->trans() : view('questions.jstree.question.text', ['question' => $this])->render();
    }

    /**
     * Get the name of the question.
     */
    public function getQuestionAttribute()
    {
        $question = $this->trans();

        if($this->markParent) {
            $question .= view('questions.datatable.question', ['question' => $this])->render();
        }

        return $question;
    }

    /**
     * Get the icon of the question.
     */
    public function getIconAttribute()
    {
        return view('questions.jstree.question.icon')->render();
    }

    /**
     * Get the datatable actions.
     */
    public function getActionsAttribute()
    {
        return view('questions.datatable.actions', ['question' => $this, 'topic' => $this->topic])->render();
    }

    /**
     * Check if question's number of answers is insufficient.
     *
     * @return boolean
     */
    public function isIncomplete()
    {
        return $this->answers->count() < env('MIN_ANSWERS_PER_QUESTION', 2);
    }

    /**
     * Check if question is parent.
     *
     * @return boolean
     */
    public function isQuestionParent()
    {
        return $this->is_parent;
    }

    /**
     * Check if question or his answers matches a given term.
     *
     * @return boolean
     */
    public function hasTerm($term)
    {
        if(strpos($this->trans(), $term) !== false) {
            return true;
        }

        foreach($this->answers as $answer) {
            if($answer->hasTerm($term)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if question has incomplete answers.
     *
     * @return boolean
     */
    public function hasIncompleteAnswers()
    {
        foreach($this->answers as $answer) {
            if($answer->isIncomplete()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if question has pending language answers.
     *
     * @return boolean
     */
    public function hasPendingLanguageAnswers($countLanguages)
    {
        foreach($this->answers as $answer) {
            if($answer->hasPendingLanguage($countLanguages)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns true if the question has pending languages.
     *
     * @return boolean
     */
    public function hasPendingLanguage($countLanguages)
    {
        return $this->translations->count() < $countLanguages;
    }

    /**
     * Check if question is reachable, either because it's parent or because it's
     * related as an answer.
     *
     * @return boolean
     */
    public function isReachable($allQuestionsFromTopic)
    {
        // Bail early if is parent question
        if($this->isQuestionParent()) return true;

        $parent = $allQuestionsFromTopic->first(function ($question) {
            return $question->isQuestionParent();
        });

        $allReachableQuestionsIds = [];
        if($parent) {
            $allReachableQuestionsIds = [$parent->id];
            $parent->getAllReachableQuestionsIds($allReachableQuestionsIds);
        }

        return in_array($this->id, $allReachableQuestionsIds);
    }

    /**
     * Get all reachable questions from question.
     *
     * @param $allReachableQuestionsIds
     */
    public function getAllReachableQuestionsIds(&$allReachableQuestionsIds)
    {
        foreach($this->answers as $answer) {
            $answer->getAllReachableQuestionsIds($allReachableQuestionsIds);
        }
    }

    /**
     * Converts Question to array for tree.
     */
    public function toTree($doneQuestionIds = [])
    {
        if(in_array($this->id, $doneQuestionIds)) {
            return [
                'text' => view('questions.jstree.question.recursion-text', ['question' => $this])->render(),
                'icon' => view('questions.jstree.question.recursion-icon')->render(),
                'children' => [],
            ];
        }

        $doneQuestionIds[] = $this->id;
        $this->doneQuestionIds = $doneQuestionIds;

        return $this->setAppends(['text', 'children', 'icon'])
            ->makeVisible(['text', 'children', 'icon'])
            ->toArray();
    }

    /**
     * Checks if question has a recursive infinite loop
     */
    public function hasInfiniteLoop($doneQuestionIds = [])
    {
        if(in_array($this->id, $doneQuestionIds)) {
            return true;
        }

        return $this->answers->map(function ($answer) {
            return $answer->toTree($this->doneQuestionIds);
        });
    }

    /**
     * Converts Question to array for datatable.
     */
    public function toDatatable()
    {
        $this->markParent = true;

        return $this->setAppends(['question', 'actions'])
            ->makeVisible(['actions'])
            ->toArray();
    }

    /**
     * Converts Question to array for select2.
     */
    public function toSelect2()
    {
        $this->simpleText = true;
        return $this->setAppends(['text'])
            ->makeVisible(['text'])
            ->toArray();
    }

    /**
     * Get the topic that owns the question.
     */
    public function topic()
    {
        return $this->belongsTo('App\Topic');
    }

    /**
     * Get the answers for the question.
     */
    public function answers()
    {
        return $this->hasMany('App\Answer', 'parent_question_id')->orderBy('order');
    }

    /**
     * Get the answers for the question by language.
     */
    public function answersByLanguage($languageId)
    {
        return $this->answers->map(function ($answer) use ($languageId) {
            return $answer->toArrayByLanguage($languageId);
        });
    }

    /**
     * Get all the question's translations.
     */
    public function translations()
    {
        return $this->hasMany('App\Translation', 'translation_id', 'question_i18n');
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
