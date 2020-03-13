<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Traits\LanguageTrait;

use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;

use App\Question;
use App\Topic;
use App\Answer;

class QuestionController extends Controller
{
    use LanguageTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['question']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param $topic_id
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $topic_id, $exceptQuestionId = null, $onlyQuestionId = null)
    {
        $topic = Topic::findOrFail($request->get('topic') ?? $topic_id);

        if ($request->ajax()) {
            return response()->json([
                'results' => $topic->questions
                            ->filter(function ($question) use ($request, $exceptQuestionId, $onlyQuestionId) {
                                if ($exceptQuestionId && $question->id == $exceptQuestionId) {
                                    return false;
                                }
                                if ($onlyQuestionId) {
                                    return $question->id == $onlyQuestionId;
                                }
                                $term = $request->get('term');
                                return empty($term) ? true : strpos($question->trans(), $term) !== false;
                            })
                            ->map(function ($question) {
                                return $question->toSelect2();
                            })
                            ->values()
            ]);
        } else {
            return view('questions.list', compact('topic'));
        }
    }

    /**
     * Display a listing of the resource in tree mode.
     *
     * @param $topic_id
     * @return \Illuminate\Http\Response
     */
    public function tree($topic_id)
    {
        $topic = Topic::findOrFail($topic_id);
        $question = Question::where([
            'is_parent' => true,
            'topic_id' => $topic_id,
        ])->firstOrFail();

        $tree = $question->toTree();

        return view('questions.tree', compact('tree', 'topic'));
    }

    /**
     * Display a listing of the resource in datatable mode.
     *
     * @param Request $request
     * @param $topic_id
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function datatable(Request $request, $topic_id)
    {
        $filter = $request->get('filter_by');
        $questions = Question::where(['topic_id' => $topic_id]);

        if(!$request->has('order')) {
            $questions = $questions->orderBy('updated_at', 'desc');
        }

        $questions = $questions->get();

        switch ($filter) {
            case 'incomplete':
                $questions = $questions->filter(function ($value, $key) {
                    return $value->isIncomplete() || $value->hasIncompleteAnswers();
                });
                break;
            case 'pending_data':
                $questions = $questions->filter(function ($value, $key) {
                    $languageCount = $this->getLanguages()->count();
                    return $value->hasPendingLanguage($languageCount) || $value->hasPendingLanguageAnswers($languageCount);
                });
                break;
            case 'not_reachable':
                $questions = $questions->filter(function ($value, $key) use ($questions) {
                    return !$value->isReachable($questions);
                });
                break;
        }

        $search = $request->get('search');
        if (is_array($search) && isset($search['value']) && !empty($search['value'])) {
            $term = $search['value'];
            $questions = $questions->filter(function ($value, $key) use ($term) {
                return $value->hasTerm($term);
            });
            $search['value'] = '';
            $request->merge(['search' => $search]);
        }

        return Datatables::of($questions->map(function ($question) {
            return $question->toDatatable();
        }))->make();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param $topic_id
     * @return \Illuminate\Http\Response
     */
    public function create($topic_id)
    {
        $topic = Topic::findOrFail($topic_id);
        $questions = $topic->questions;
        $languages = $this->getLanguages();

        if ($languages->isEmpty()) {
            return redirect()->route('topics.questions.index')->with('error', __('No languages found.'));
        }

        return view('questions.create', compact('topic', 'questions', 'languages'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreQuestionRequest $request
     * @param $topic_id
     * @return \Illuminate\Http\Response
     */
    public function store(StoreQuestionRequest $request, $topic_id)
    {
        $topic = Topic::findOrFail($topic_id);
        $questions = $topic->questions;

        $names = $request->ajax() ? $request->get('new_question') : $request->get('question');

        $translationId = $this->createTranslationTerms($names);

        $question = new Question([
            'question_i18n' => $translationId,
            'topic_id' => $topic_id,
            'is_parent' => $questions->count() == 0,
        ]);

        $question->save();

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Question saved successfully.',
                'data' => [
                    'id' => $question->id
                ],
            ]);
        } else {
            $this->storeAnswers($request, $question);
            return redirect()->route('topics.questions.index', $topic_id)->with('success', 'New question created successfully.');
        }
    }

    /**
     * Store answers from a given question.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param Question $question
     * @return boolean
     */
    private function storeAnswers(Request $request, $question)
    {
        $answers = $request->get('answers');

        $i = 0;
        foreach ($answers as $answer) {
            $this->createOrUpdateAnswer($answer, $i++, $question->id, false);
        }

        return true;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->route('topics.questions.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $topic_id
     * @param $question_id
     * @return \Illuminate\Http\Response
     */
    public function edit($topic_id, $question_id)
    {
        $question = Question::findOrFail($question_id);
        $answersRelation = $question->answers;
        $topic = Topic::findOrFail($topic_id);
        $questions = $topic->questions;
        $languages = $this->getLanguages();

        $answers = [];
        foreach ($answersRelation as $answer) {
            $url = $text = [];
            foreach ($languages as $language) {
                $text['text-' . $language->id] = $answer->trans($language->id, true);
                $url['url-' . $language->id] = $answer->getUrlAttribute($language->id, true);
            }

            $answers[] = array_merge($url, $text, [
                'id' => $answer->id,
                'type' => $answer->type,
                'question' => $answer->answer_question_id,
            ]);
        }

        return view('questions.edit', compact('question', 'topic', 'answers', 'questions', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateQuestionRequest $request
     * @param $topic_id
     * @param $question_id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateQuestionRequest $request, $topic_id, $question_id)
    {
        $question = Question::findOrFail($question_id);
        $questions = $request->get('question');

        $this->updateTranslationTerms($question->translations, $questions);

        $this->updateAnswers($request, $question);

        return redirect()->route('topics.questions.index', $topic_id)->with('success', __('Question updated successfully.'));
    }

    /**
     * Update answers from a given question.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param Question $question
     * @return boolean
     */
    private function updateAnswers(Request $request, $question)
    {
        $newAnswers = $request->get('answers');
        $oldAnswers = $question->answers->pluck('id');

        $i = 0;
        foreach ($newAnswers as $newAnswer) {
            $this->createOrUpdateAnswer($newAnswer, $i++, $question->id, true);

            $currentId = $newAnswer['id'];
            if ($currentId && $oldAnswers->contains($currentId)) {
                $oldAnswers = $oldAnswers->reject(function ($element) use ($currentId) {
                    return $element == $currentId;
                });
            }
        }

        if ($oldAnswers->isNotEmpty()) {
            $oldAnswers->each(function ($item, $key) {
                Answer::findOrFail($item)->delete();
            });
        }

        return true;
    }

    /**
     * Create or update and store an answer.
     *
     * @param $questionId
     * @param $order
     * @param $answer
     * @return Answer
     */
    private function createOrUpdateAnswer($answer, $order, $questionId, $byIds)
    {
        return isset($answer['id']) && is_numeric($answer['id']) ?
            $this->updateAnswer($answer['type'], $answer['id'], $order, $answer) :
            $this->createAnswer($answer['type'], $questionId, $order, $answer, $byIds);
    }

    /**
     * Creates and stores a new answer.
     *
     * @param $type
     * @param $questionId
     * @param $order
     * @param $answer
     * @return Answer
     */
    private function createAnswer($type, $questionId, $order, $answer, $byIds)
    {
        $names = [];
        foreach ($answer as $key => $value) {
            if (strpos($key, 'text-') !== false) {
                $code = explode("-", $key);
                $names[$code[1]] = $value;
            }
        }

        $translationId = $this->createTranslationTerms($names, $byIds);

        $data = [
            'answer_i18n' => $translationId,
            'order' => $order,
            'parent_question_id' => $questionId,
        ];

        switch ($type) {
            case 'url':
                $urls = [];
                foreach ($answer as $key => $value) {
                    if (strpos($key, 'url-') !== false) {
                        $code = explode("-", $key);
                        $urls[$code[1]] = $value;
                    }
                }

                $translationId = $this->createTranslationTerms($urls, $byIds);

                $data = array_merge($data, [
                    'url_i18n' => $translationId,
                ]);

                break;
            case 'question':
                $data = array_merge($data, [
                    'answer_question_id' => $answer['question'],
                ]);
                break;
        }

        $answer = new Answer($data);
        $answer->save();

        return $answer;
    }

    /**
     * Updates and stores an existing answer.
     *
     * @param $type
     * @param $answerId
     * @param $order
     * @param $data
     * @return Answer
     */
    private function updateAnswer($type, $answerId, $order, $data)
    {
        $answer = Answer::findOrFail($answerId);

        $names = [];
        foreach ($data as $key => $value) {
            if (strpos($key, 'text-') !== false) {
                $code = explode("-", $key);
                $names[$code[1]] = $value;
            }
        }

        $this->updateTranslationTerms($answer->translations, $names);

        $answer->order = $order;

        switch ($type) {
            case 'url':
                $answer->question()->dissociate();

                $urls = [];
                foreach ($data as $key => $value) {
                    if (strpos($key, 'url-') !== false) {
                        $code = explode("-", $key);
                        $urls[$code[1]] = $value;
                    }
                }

                $updated = $this->updateTranslationTerms($answer->translationsUrl, $urls);

                if (!$updated && is_array($urls)) {
                    $translationId = $this->createTranslationTerms($urls, true);
                    $answer->url_i18n = $translationId;
                }

                break;
            case 'question':
                $answer->question()->associate(Question::findOrFail($data['question']));
                break;
        }

        $answer->save();

        return $answer;
    }

    /**
     * Make the question the parent of the topic.
     *
     * @param \Illuminate\Http\Request $request
     * @param $topic_id
     * @param $question_id
     * @return \Illuminate\Http\Response
     */
    public function makeParent(Request $request, $topic_id, $question_id)
    {
        Question::where('topic_id', $topic_id)->update(['is_parent' => 0]);
        Question::find($question_id)->update(['is_parent' => 1]);

        return redirect()->route('topics.questions.index', $topic_id)->with('success', __('Question set as first of the topic successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $topic_id
     * @param $question_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($topic_id, $question_id)
    {
        $question = Question::findOrFail($question_id);

        if ($question->is_parent) {
            return redirect()->route('topics.questions.index', $topic_id)->with('error', __('First question can not being deleted.'));
        }

        $incompleteQuestionsBeforeDelete = Question::where(['topic_id' => $topic_id])->get()->filter(function ($value, $key) {
            return $value->isIncomplete() || $value->hasIncompleteAnswers();
        })->count();

        try {
            $question->delete();
        } catch (\Exception $exception) {
            return redirect()->route('topics.questions.index', $topic_id)->with('error', __('An error has occurred while deleting the question.'));
        }

        $incompleteQuestionsAfterDelete = Question::where(['topic_id' => $topic_id])->get()->filter(function ($value, $key) {
            return $value->isIncomplete() || $value->hasIncompleteAnswers();
        })->count();

        $response = redirect()->route('topics.questions.index', $topic_id)->with('success', __('Question deleted successfully.'));

        return $incompleteQuestionsBeforeDelete < $incompleteQuestionsAfterDelete
                ? $response->with('warning', sprintf(__('Deletion of this question has generated %s incomplete question(s) from those that were previously linked to.'), ($incompleteQuestionsAfterDelete - $incompleteQuestionsBeforeDelete)))
                : $response;
    }

    /**
     * Copies a resource in storage.
     *
     * @param Request $request
     * @param $topic_id
     * @return \Illuminate\Http\Response
     */
    public function copy(Request $request, $topic_id)
    {
        $request->validate([
            'question' => 'required|numeric|exists:App\Question,id'
        ]);

        $question = Question::findOrFail($request->get('question'));
        $topic = Topic::findOrFail($topic_id);

        $newQuestionId = $this->copyQuestion($question, $topic);

        return response()->json([
            'message' => __('Question copied successfully.'),
            'data' => [
                'id' => $newQuestionId
            ],
        ]);
    }

    /**
     * Copies a question and it's answers recursively
     *
     * @param Question $question
     * @param Topic $topic
     * @return integer
     */
    public function copyQuestion($question, $topic, &$alreadyDoneQuestions = [])
    {
        // Bail if already copied question
        if (in_array($question->id, array_keys($alreadyDoneQuestions))) {
            return $alreadyDoneQuestions[$question->id];
        }

        $names = $question->translations->mapWithKeys(function ($translation) {
            return [$translation->language_id => $translation->getTerm()];
        })->toArray();

        $translationId = $this->createTranslationTerms($names, true);

        $newQuestion = new Question([
            'question_i18n' => $translationId,
            'topic_id' => $topic->id,
            'is_parent' => $topic->questions->count() == 0,
        ]);
        $newQuestion->save();
        $alreadyDoneQuestions[$question->id] = $newQuestion->id;

        foreach ($question->answers as $answer) {
            $answers = $answer->translations->mapWithKeys(function ($translation) {
                return [$translation->language_id => $translation->getTerm()];
            })->toArray();

            $translationId = $this->createTranslationTerms($answers, true);

            $data = [
                'answer_i18n' => $translationId,
                'order' => $answer->order,
                'parent_question_id' => $newQuestion->id,
            ];

            if ($answer->isUrlAnswer()) {
                $urls = $answer->translationsUrl->mapWithKeys(function ($translation) {
                    return [$translation->language_id => $translation->getTerm()];
                })->toArray();

                $translationId = $this->createTranslationTerms($urls, true);

                $data = array_merge($data, [
                    'url_i18n' => $translationId,
                ]);
            } elseif ($answer->isQuestionAnswer()) {
                $relatedQuestionId = optional($answer->question)->id;
                if (!is_null($relatedQuestionId)) {
                    $relatedQuestionId = $this->copyQuestion(Question::findOrFail($relatedQuestionId), $topic, $alreadyDoneQuestions);
                }
                $data = array_merge($data, [
                    'answer_question_id' => $relatedQuestionId,
                ]);
            }
            $newAnswer = new Answer($data);
            $newAnswer->save();
        }

        return $newQuestion->id;
    }

    /**
     * API
     *
     * Gets the question.
     *
     * @param $iso_code
     * @param $topic_id
     * @param $question_id
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function question($iso_code, $topic_id = null, $question_id = null)
    {
        $language = $this->getLanguageByIsoCode($iso_code);

        if (is_numeric($question_id)) {
            $question = Question::findOrFail($question_id);
        } elseif (is_numeric($topic_id)) {
            $question = Question::where([
                'is_parent' => true,
                'topic_id' => $topic_id,
            ])->firstOrFail();
        } else {
            return abort(400);
        }

        return collect([
            'id' => $question->id,
            'question' => $question->trans($language->id),
            'answers' => $question->answersByLanguage($language->id)
        ]);
    }
}
