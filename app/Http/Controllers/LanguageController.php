<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Traits\LanguageTrait;
use Illuminate\Validation\Rule;

use App\Language;
use App\Copy;

class LanguageController extends Controller
{
    use LanguageTrait;

    /**
     * Avalaible language copies
     *
     * @var $copies
     */
    protected $copies;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['copies']]);

        $this->copies = $this->getAvailableCopies();
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $language_id
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $exceptLanguageId = null, $onlyLanguageId = null)
    {
        if ($request->ajax()) {
            return response()->json([
                'results' => Language::all()
                    ->filter(function ($language) use ($request, $exceptLanguageId, $onlyLanguageId) {
                        if ($exceptLanguageId && $language->id == $exceptLanguageId) {
                            return false;
                        }
                        if ($onlyLanguageId) {
                            return $language->id == $onlyLanguageId;
                        }
                        $term = $request->get('term');
                        return empty($term) ? true : strpos($language->name, $term) !== false;
                    })
                    ->map(function ($language) {
                        return $language->toSelect2();
                    })
                    ->values()
            ]);
        } else {
            return view('languages.index');
        }
    }

    /**
     * Display a listing of the resource in datatable mode.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function datatable(Request $request)
    {
        $languages = Language::all();

        return Datatables::of($languages->map(function ($language) {
            return $language->toDatatable();
        }))->make();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $languages = $this->getLanguages();
        $copies = $this->copies;

        return view('languages.create', compact('languages', 'copies'));
    }

    /**
     * Get the copies rules.
     *
     * @return array
     */
    public function getCopiesRules()
    {
        $copiesRules = [];

        foreach ($this->copies->keys() as $key) {
            $copiesRules[$key] = 'required|max:255';
        }

        return $copiesRules;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $copiesRules = $this->getCopiesRules();

        $request->validate(array_merge($copiesRules, [
            'name' => 'required|unique:languages,name',
            'iso_code' => 'required|unique:languages,iso_code|size:2',
            'support_language' => 'sometimes|exists:App\Language,id',
        ]));

        $languages = $this->getLanguages();

        $language = new Language([
            'name' => $request->get('name'),
            'iso_code' => $request->get('iso_code'),
            'is_parent' => $languages->count() == 0,
            'language_id' => $request->get('support_language'),
        ]);

        $language->save();

        $this->storeCopies($request, $language);

        return redirect()->route('languages.index')->with('success', __('New language created successfully.'));
    }

    /**
     * Store copies from a given language.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param Language $language
     * @return boolean
     */
    private function storeCopies(Request $request, $language)
    {
        foreach ($this->copies->keys() as $key) {
            if ($request->get($key)) {
                $this->createOrUpdateCopy($language, $key, $request->get($key));
            }
        }

        return true;
    }

    /**
     * Create or update and store a copy.
     *
     * @param $language
     * @param $key
     * @param $value
     * @return Copy
     */
    private function createOrUpdateCopy($language, $key, $value)
    {
        return Copy::updateOrCreate(
            ['name' => $key, 'language_id' => $language->id],
            ['value' => $value]
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->route('languages.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $language = Language::findOrFail($id);
        $languages = $this->getLanguages();
        $copies = $this->copies;

        return view('languages.edit', compact('language', 'languages', 'copies'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Never use 'ignore' when validating with user given $id from $request because it could lead to SQL Injection
        // Instead first search it or fail
        $language = Language::findOrFail($id);

        $copiesRules = $this->getCopiesRules();

        $request->validate(array_merge($copiesRules, [
            'name' => [
                'required',
                Rule::unique('languages', 'name')->ignore($language->id),
            ],
            'iso_code' => [
                'required',
                'size:2',
                Rule::unique('languages', 'iso_code')->ignore($language->id),
            ],
            'support_language' => 'sometimes|exists:App\Language,id',
        ]));

        $language->name = $request->get('name');
        $language->iso_code = $request->get('iso_code');
        $language->language_id = $request->get('support_language');
        $language->save();

        $this->storeCopies($request, $language);

        return redirect()->route('languages.index')->with('success', __('Language updated successfully.'));
    }

    /**
     * Make the language the main.
     *
     * @param \Illuminate\Http\Request $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function makeParent(Request $request, $id)
    {
        Language::where(['is_parent' => 1])->update(['is_parent' => 0]);
        Language::find($id)->update(['is_parent' => 1]);

        return redirect()->route('languages.index')->with('success', __('Language set as main successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $language = Language::findOrFail($id);

        if ($language->is_parent) {
            return redirect()->route('languages.index')->with('error', __('Main language can not being deleted.'));
        }

        try {
            $language->delete();
        } catch (\Exception $exception) {
            return redirect()->route('languages.index')->with('error', __('An error has occurred while deleting the language.'));
        }

        return redirect()->route('languages.index')->with('success', __('Language deleted successfully.'));
    }

    /**
     * API
     *
     * Gets the copies from the language by the ISO Code.
     *
     * @param $iso_code
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function copies($iso_code)
    {
        $language = $this->getLanguageByIsoCode($iso_code);

        return $language->copies->mapWithKeys(function ($copy, $key) {
            return [$copy->name => $copy->value];
        });
    }
}
