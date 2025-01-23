<?php

namespace App\Http\Controllers;

use App\Models\Time;
use App\Models\Person;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TimeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $time = Time::all();
        // ('people')に$peopleが代入される
        return redirect()->route('people.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
{
    $person = Person::findOrFail($request->people_id);
    return redirect()->route('time.edit', ['people_id' => $person->id]);
}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
{
    // バリデーションルールを設定
    $request->validate([
        'start_time' => 'nullable',
        'end_time' => 'nullable',
        'school' => 'nullable',
        'is_absent' => 'nullable',
        'pick_up' => 'nullable',
        'send' => 'nullable',
    ]);

    // すべてのフィールドが空であるかをチェック
    if (empty($request->start_time) && empty($request->end_time) && empty($request->school) &&
        empty($request->is_absent) && empty($request->pick_up) && empty($request->send)) {
        return redirect()->back()->withErrors(['fields' => 'いずれか登録してください。'])->withInput();
    }

       //JSON形式からboolean型に更新するため、コメントアウト
        // チェックボックスのデータをJSON形式に変換
        // $pick_up = json_encode($request->input('pick_up', []));
        // $send = json_encode($request->input('send', []));


        // 送迎のチェックボックスの値を直接booleanとして扱う
        $time = Time::create([
            'people_id' => $request->people_id,
            'date' => $request->date,
            //$is_absentがtrueの場合はnullを代入
            'start_time' =>$request->start_time,
            'end_time' =>$request->end_time,
            'school' => $request->school,
            'is_absent' => $request->boolean('is_absent'),
            'pick_up' => $request->boolean('pick_up'),
            'send' => $request->boolean('send'),
        ]);
        $person = Person::findOrFail($request->people_id);
    
        // 二重送信防止
        $request->session()->regenerateToken();

        // セッションに$personを保存
        session(['selected_person' => $person]);

        return redirect()->route('people.index')->with('success', '登録が成功しました。');
        // return redirect()->route('people.content', ['id' => $person->id])->with('success', '登録が成功しました。');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Time  $time
     * @return \Illuminate\Http\Response
     */
    public function show($people_id)
{
    $person = Person::findOrFail($people_id);
    $time = $person->times;
   
    // return view('people',compact('time'));
    return redirect()->route('people.index');
}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Time  $time
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $people_id)
{
    $person = Person::findOrFail($people_id);
    return view('timeedit', ['id' => $person->id],compact('person'));
}

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Time  $time
     * @return \Illuminate\Http\Response
     */
     
    public function change(Request $request, $people_id, $id)
    {
        $person = Person::findOrFail($people_id);
        // $lastTime = $person->times->last();
        $lastTime = Time::findOrFail($id);


        // 利用時間合計を計算するためのコード↓
        $startTime = Carbon::parse($lastTime->start_time);
        $endTime = Carbon::parse($lastTime->end_time);
        // dd($startTime, $endTime);

        // 開始時間と終了時間の差を計算
        $diffInHours = $startTime->diffInHours($endTime);
        // dd($diffInHours);
        $diffInMinutes = $startTime->diffInMinutes($endTime) % 60;

        // 合計利用時間を文字列にフォーマット
        $totalUsageTime = $diffInHours . '時間' . $diffInMinutes . '分';

        return view('timechange', compact('person','totalUsageTime', 'lastTime'));
    }
    
    public function update(Request $request, Time $time, $id)
{
    // IDをリクエストから取得
    $id = $request->id;
    // Time モデルのレコードを取得
    $time = Time::find($id);

    // レコードが存在しない場合のエラーハンドリング
    if (!$time) {
        return redirect()->back()->with('error', '指定されたデータが見つかりません。');
    }

    // フォームの値を更新
    $form = $request->all();

    // is_absentが1の場合、他のカラムをクリア
    if ($request->boolean('is_absent')) {
        $form['start_time'] = null;
        $form['end_time'] = null;
        $form['school'] = null;
        $form['pick_up'] = false;
        $form['send'] = false;
    } else {
        // is_absentが0の場合、通常の値を設定
        $form['start_time'] = $request->start_time;
        $form['end_time'] = $request->end_time;
        $form['school'] = $request->school;
        $form['pick_up'] = $request->boolean('pick_up');
        $form['send'] = $request->boolean('send');
    }

    $form['is_absent'] = $request->boolean('is_absent');

    $time->fill($form)->save();

    $people = Person::all();
    // 二重送信防止
    $request->session()->regenerateToken();

    return redirect()->route('people.index');
}
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Time  $time
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) 
        {
            $lastTime = Time::find($id);       
            if ($lastTime) {
        
              $lastTime->delete();
            }       
            return redirect()->route('people.index')->with('success', '削除が完了しました。');
        }
}