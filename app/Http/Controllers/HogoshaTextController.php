<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HogoshaText;
use App\Models\Person;
use Illuminate\Support\Facades\Auth;

class HogoshaTextController extends Controller
{
  public function show(Request $request, $people_id)
{
    $person = Person::findOrFail($people_id);
    $selectedDate = $request->input('selected_date', \Carbon\Carbon::now()->toDateString());
    $selectedDateStart = \Carbon\Carbon::parse($selectedDate)->startOfDay();    
    $selectedDateEnd = \Carbon\Carbon::parse($selectedDate)->endOfDay();

    $hogoshatexts = HogoshaText::where('people_id', $people_id)
                   ->whereBetween('created_at', [$selectedDateStart, $selectedDateEnd])
                   ->get();

    if ($request->ajax()) {
        $view = view('hogoshatext', compact('person', 'selectedDate', 'hogoshatexts'))
            ->renderSections()['hogoshatexts'];
        return response()->json(['html' => $view]);
    }

    return view('hogoshatext', compact('person', 'selectedDate', 'hogoshatexts'));
}

    public function store(Request $request)
    {
        $request->validate([
            'notebook' => 'required|string|max:1000',
        ], [
            'notebook.required' => 'フォームに入力してください。',
        ]);

        HogoshaText::create([
            'people_id' => $request->people_id,
            'notebook' => $request->notebook,
        ]);

        $people = Person::all();
        $request->session()->regenerateToken();
        return redirect()->route('condition.edit')->with('success', '正常に送信されました');
    }

    public function change(Request $request, $people_id, $id)
    {
        $person = Person::findOrFail($people_id);
        $lastHogoshaText = HogoshaText::findOrFail($id);
        return view('hogoshatextchange', compact('person', 'lastHogoshaText'));
    }

    public function update(Request $request, $people_id, $id)
    {
        //データ更新
        $person = Person::find($request->people_id);
        $notebook = HogoshaText::findOrFail($id);

        // データ更新
        $notebook->notebook = $request->notebook;

        $notebook->save();

        return redirect()->route('condition.edit')->with('success', '更新されました。');
    }
}