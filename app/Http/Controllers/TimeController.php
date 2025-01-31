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
            'start_hour' => 'nullable|numeric|between:0,23',
            'start_minute' => 'nullable|numeric|between:0,55',
            'end_hour' => 'nullable|numeric|between:0,23',
            'end_minute' => 'nullable|numeric|between:0,55',
            'school' => 'nullable',
            'is_absent' => 'nullable',
            'pick_up' => 'nullable',
            'send' => 'nullable',
        ]);
    
        // 時間を結合して形式を整える
        $start_time = null;
        $end_time = null;
        
        if ($request->start_hour !== null && $request->start_minute !== null) {
            $start_time = sprintf('%02d:%02d', $request->start_hour, $request->start_minute);
        }
        
        if ($request->end_hour !== null && $request->end_minute !== null) {
            $end_time = sprintf('%02d:%02d', $request->end_hour, $request->end_minute);
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
            'start_time' =>$start_time,
            'end_time' =>$end_time,
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
    $id = $request->id;
    $time = Time::find($id);

    if (!$time) {
        return redirect()->back()->with('error', '指定されたデータが見つかりません。');
    }

    // 時間を結合して形式を整える
    $start_time = null;
    $end_time = null;
    
    if ($request->start_hour !== null && $request->start_minute !== null) {
        $start_time = sprintf('%02d:%02d', $request->start_hour, $request->start_minute);
    }
    
    if ($request->end_hour !== null && $request->end_minute !== null) {
        $end_time = sprintf('%02d:%02d', $request->end_hour, $request->end_minute);
    }

    // is_absentが1の場合、他のカラムをクリア
    if ($request->boolean('is_absent')) {
        $form = $request->all();
        $form['start_time'] = null;
        $form['end_time'] = null;
        $form['school'] = null;
        $form['pick_up'] = false;
        $form['send'] = false;
    } else {
        $form = $request->all();
        $form['start_time'] = $start_time;
        $form['end_time'] = $end_time;
        $form['pick_up'] = $request->boolean('pick_up');
        $form['send'] = $request->boolean('send');
    }

    $form['is_absent'] = $request->boolean('is_absent');
    
    $time->fill($form)->save();

    $person = Person::findOrFail($request->people_id);
    $request->session()->regenerateToken();
    session(['selected_person' => $person]);

    return redirect()->route('people.index')->with('success', '変更が完了しました');
}
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Time  $time
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $people_id, $id) 
{
    $lastTime = Time::find($id);       
    if ($lastTime) {
        $lastTime->delete();
    }  

    $person = Person::findOrFail($request->people_id);
    // セッションに$personを保存
    session(['selected_person' => $person]);

    return redirect()->route('people.index')->with('success', '削除が完了しました。');
}
}