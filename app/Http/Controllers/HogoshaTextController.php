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
    $user = Auth::user();
    $selectedDate = $request->input('selected_date', \Carbon\Carbon::now()->toDateString());
    // $selectedDateStart = \Carbon\Carbon::parse($selectedDate)->startOfDay();    
    // $selectedDateEnd = \Carbon\Carbon::parse($selectedDate)->endOfDay();

    $hogoshatexts = HogoshaText::where('people_id', $people_id)
                //    ->whereBetween('created_at', [$selectedDateStart, $selectedDateEnd])
                   ->get();

    if ($request->ajax()) {
        $view = view('hogoshatext', compact('person', 'selectedDate', 'hogoshatexts'))
            ->renderSections()['hogoshatexts'];
        return response()->json(['html' => $view]);
    }

    // もしログインしているユーザーが存在するかチェックする場合
    if ($user) {
        $user_name = $user->name;
        $user_identifier = $user->id;
    } else {
        // ユーザーが存在しない場合の処理
        $user_name = 'Guest';
        $user_identifier = Str::random(20);
    }
    // ユーザーIDをセッションに登録
    //   $user_identifier = $request->session()->get('user_identifier', Str::random(20));
      session(['user_identifier' => $user_identifier]);

    // ユーザー識別子がなければランダムに生成してセッションに登録
    if ($request->session()->missing('user_identifier')) {
        session(['user_identifier' => Str::random(20)]);
    }
    // ユーザー名を変数に登録（デフォルト値：Guest）
    if ($request->session()->missing('user_name')) {
        session(['user_name' => $user_name]);
    }

    // チャット画面を表示する際に、未読メッセージを既読にする
    HogoshaText::where('people_id', $people_id)
        ->where('user_identifier', '!=', $user_identifier)
        ->where('is_read', false)
        ->update(['is_read' => true]);

    $unreadMessages = HogoshaText::where('people_id', $people_id)
    ->where('is_read', false)
    ->count();

    return view('hogoshatext', compact('person', 'selectedDate', 'hogoshatexts','user_name',  'unreadMessages'));


}

    public function store(Request $request, $people_id)
    {
        $user = Auth::user();
             if ($user) {
                 $user_name = $user->last_name . ' ' . $user->first_name;
                 $user_identifier = $user->id;
     
                 if ($user->hasAnyRole(['facility staff administrator', 'facility staff user', 'facility staff reader'])) {
                     $facility = $user->facility_staffs()->first();
                     if ($facility) {
                         $user_name = $facility->facility_name;
                     }
                 }
             } else {
                 $user_name = 'Guest';
                 $user_identifier = Str::random(20);
             }
     
             session(['user_name' => $user_name]);
             session(['user_identifier' => $user_identifier]);
             \Log::info('Session user_name: ' . session('user_name'));

        $request->validate([
            'notebook' => 'required|string|max:1000',
        ], [
            'notebook.required' => 'フォームに入力してください。',
            'notebook.max' => '1000文字以下で記入してください。',
        ]);

        HogoshaText::create([
            'people_id' => $request->people_id,
            'notebook' => $request->notebook,
            'user_identifier' => $user_identifier,
            'last_name' => $user ? $user->last_name : null,
            'first_name' => $user ? $user->first_name : null,
        ]);

        $people = Person::all();
        $request->session()->regenerateToken();
        return redirect()->route('hogoshatext.show', ['people_id' => $people_id])->with('success', '正常に送信されました');
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