<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use App\Traits\LanguageTrait;

class UpdateQuestionRequest extends FormRequest
{
    use LanguageTrait;

    /**
     * @var int $minAnswers Minimum accepted answers
     */
    protected $minAnswers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->minAnswers = env('MIN_ANSWERS_PER_QUESTION', 2);
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    protected function getRules($type, $rule)
    {
        $languages = $this->getLanguages()->reject(function ($lang) {
            return $lang->isMainLanguage();
        });

        return $languages->mapWithKeys(function ($lang) use ($type, $rule) {
            return ['answers.*.' . $type . '-' . $lang->id => 'nullable|' . $rule];
        })->toArray();
    }

    protected function getTextRules()
    {
        return array_merge($this->getRules('text', 'string'), [
            'answers.*.text-' . $this->getParentLanguageId() => 'required|string'
        ]);
    }

    protected function getUrlRules()
    {
        return array_merge($this->getRules('url', 'url'), [
            'answers.*.url-' . $this->getParentLanguageId() => 'exclude_unless:answers.*.type,url|url'
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'question.' . $this->getParentLanguageId() => 'required',
            'answers' => 'required|array|min:' . $this->minAnswers,
            'answers.*.type' => 'required|in:url,question',
            'answers.*.question' => 'exclude_unless:answers.*.type,question|exists:App\Question,id',
        ];

        return array_merge($rules, $this->getTextRules(), $this->getUrlRules());
    }
}
