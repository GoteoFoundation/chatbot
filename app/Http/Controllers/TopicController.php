<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Traits\LanguageTrait;

use App\Topic;

class TopicController extends Controller
{
    use LanguageTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return response()->json([
                'results' => Topic::all()
                    ->filter(function ($topic) use ($request) {
                        $topicId = $request->get('topic');
                        if ($topic->id == $topicId) {
                            return false;
                        }
                        $term = $request->get('term');
                        return empty($term) ? true : strpos($topic->name, $term) !== false;
                    })
                    ->map(function ($topic) {
                        return $topic->toSelect2();
                    })
                    ->values()
            ]);
        } else {
            return view('topics.index');
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
        $topics = Topic::all();

        return Datatables::of($topics->map(function ($topic) {
            return $topic->toDatatable();
        }))->make();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('topics.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $topic = new Topic([
            'name' => $request->get('name'),
        ]);

        $topic->save();

        return redirect()->route('topics.index')->with('success', __('New topic created successfully.'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->route('topics.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $topic = Topic::findOrFail($id);

        return view('topics.edit', compact('topic'));
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
        $request->validate([
            'name' => 'required'
        ]);

        $topic = Topic::findOrFail($id);
        $topic->name = $request->get('name');
        $topic->save();

        return redirect()->route('topics.index')->with('success', __('Topic updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $topic = Topic::findOrFail($id);

        try {
            $topic->delete();
        } catch (\Exception $exception) {
            return redirect()->route('topics.index')->with('error', __('An error has occurred while deleting the topic.'));
        }

        return redirect()->route('topics.index')->with('success', __('Topic deleted successfully.'));
    }
}
