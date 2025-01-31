<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\Hogosha;
use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\HogoshaText;

class HogoshaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        \Log::info('HogoshaController index method started.');

    //   / 全件データ取得して一覧表示する↓
        
        // 現在ログインしているユーザーを取得
      $user = Auth::user();

      // ユーザーが関連付けられている全てのPerson（利用者）を取得
      $people = $user->people_family()->get();
        // ('people')に$peopleが代入される



        // 未読メッセージの情報を各 Person に追加
        foreach ($people as $person) {
            $unreadChats = Chat::where('people_id', $person->id)
                                ->where('is_read', false)
                                ->where('user_identifier', '!=', $user->id)
                                ->exists();
        
            $person->unreadChats = $unreadChats;
            \Log::info("Person {$person->id} unread messages: " . ($unreadChats ? 'true' : 'false'));
    }
        // 施設から連絡があった場合(hogoshatextビュー)の未読メッセージの情報を各 Person に追加↓
        foreach ($people as $person) {
            $unreadMessages = HogoshaText::where('people_id', $person->id)
                                ->where('is_read', false)
                                ->where('user_identifier', '!=', $user->id)
                                ->exists();
        
            $person->unreadMessages = $unreadMessages;
            \Log::info("Person {$person->id} unread messages: " . ($unreadMessages ? 'true' : 'false'));
    }
        return view('hogosha', compact('hogosha', 'people'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
{
    $person = Person::findOrFail($request->people_id);
    return redirect()->route('hogosha.edit', ['people_id' => $person->id]);
}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       $storeData = $request->validate([

        ]);
        // バリデーションした内容を保存する↓

        $hogosha = Hogosha::create([
        'people_id' => $request->people_id,
        'condition' => $request->condition,
        'temperature_created_at' => $request->temperature_created_at,
        'temperature' => $request->temperature,
        'ben_created_at' => $request->ben_created_at,
        'ben_condition' => $request->ben_condition,
        'urine_created_at' => $request->urine_created_at,
        'food_created_at' => $request->food_created_at,
        'nyuuyoku' => $request->nyuuyoku,
        'oyatsu' => $request->oyatsu,
        'bikou' => $request->bikou,
    ]);
    // return redirect('people/{id}/edit');
     $people = Person::all();

    return view('people', compact('hogosha', 'people'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Food  $food
     * @return \Illuminate\Http\Response
     */
    public function show($id)
{
 // 現在ログインしているユーザーを取得
 $user = Auth::user();

 // ユーザーが関連付けられている全てのPerson（利用者）を取得
 $people = $user->people_family()->get();
  

    // 未読メッセージの情報を各 Person に追加
    foreach ($people as $person) {
        $unreadChats = Chat::where('people_id', $person->id)
                            ->where('is_read', false)
                            ->where('user_identifier', '!=', $user->id)
                            ->exists();
    
        $person->unreadChats = $unreadChats;
        \Log::info("Person {$person->id} unread messages: " . ($unreadChats ? 'true' : 'false'));
}

    // 施設から連絡があった場合(hogoshatextビュー)の未読メッセージの情報を各 Person に追加↓
    foreach ($people as $person) {
        $unreadMessages = HogoshaText::where('people_id', $person->id)
                            ->where('is_read', false)
                            ->where('user_identifier', '!=', $user->id)
                            ->exists();
    
        $person->unreadMessages = $unreadMessages;
        \Log::info("Person {$person->id} unread messages: " . ($unreadMessages ? 'true' : 'false'));
    }
    return view('people', compact('hogoshas'));
}
    // $temperature = Temperature::findOrFail($id);

    // return view('temperaturelist', compact('temperature'));


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Food  $food
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $people_id)
{
    $person = Person::findOrFail($people_id);
    return view('hogosha', ['id' => $person->id],compact('person'));
}

public function change(Request $request, $people_id)
    {
        $person = Person::findOrFail($people_id);
        $lastHogosha = $person->hogoshas->last();

        return view('hogoshachange', compact('person', 'lastHogosha'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Food  $food
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Hogosha $hogosha)
    {
    //データ更新
        $person = Person::find($request->people_id);
        $hogosha->people_id = $person->id;
        $hogosha->condition = $request->condition;
        $hogosha->temperature_created_at = $request->temperature_created_at;
        $hogosha->temperature = $request->temperature;
        $hogosha->ben_created_at = $request->ben_created_at;
        $hogosha->ben_condition = $request->ben_condition;
        $hogosha->urine_created_at = $request->urine_created_at;
        $hogosha->food_created_at = $request->food_created_at;
        $hogosha->nyuuyoku = $request->nyuuyoku;
        $hogosha->oyatsu = $request->oyatsu;
        $hogosha->bikou = $request->bikou;
        $hogosha->save();

        $people = Person::all();

        return view('people', compact('hogosha', 'people'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Food  $food
     * @return \Illuminate\Http\Response
     */
    public function destroy(Food $food)
    {
        //
    }
}
