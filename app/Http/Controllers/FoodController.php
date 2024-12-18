<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\Person;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FoodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     
    public function index() {
   //** ↓ 下をコピー ↓ **    
    
		
    $foods = Food::orderBy('created_at', 'asc')->get();

    return view('foods', ['foods' => $foods]);
    
    //** ↑ 上をコピー ↑ **
}
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
//    public function create(Request $request)
// {
//     $person = Food::findOrFail($request->people_id);
   
//     return view('people', ['people' => Person::all()]);
// }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       $storeData = $request->validate([
        'lunch' => 'required_without_all:lunch_bikou,oyatsu,oyatsu_bikou',
        'lunch_bikou' => 'required_without_all:lunch,oyatsu,oyatsu_bikou',
        'oyatsu' => 'required_without_all:lunch,lunch_bikou,oyatsu_bikou',
        'oyatsu_bikou' => 'required_without_all:lunch,lunch_bikou,oyatsu',
    ], [
        'lunch.required_without_all' => '昼食、昼食備考、おやつ、おやつ備考のいずれかを入力してください。',
        'lunch_bikou.required_without_all' => '昼食、昼食備考、おやつ、おやつ備考のいずれかを入力してください。',
        'oyatsu.required_without_all' => '昼食、昼食備考、おやつ、おやつ備考のいずれかを入力してください。',
        'oyatsu_bikou.required_without_all' => '昼食、昼食備考、おやつ、おやつ備考のいずれかを入力してください。',
    ]);

    // チェックボックスや選択肢の値をクリーンアップ
    $lunch = $request->lunch === '登録なし' ? null : $request->lunch;
    $oyatsu = $request->oyatsu === '登録なし' ? null : $request->oyatsu;

    // 少なくとも1つのフィールドが入力されているか確認
    if (!$lunch && !$request->lunch_bikou && !$oyatsu && !$request->oyatsu_bikou) {
        return redirect()->back()->withErrors(['input_required' => '昼食、昼食備考、おやつ、おやつ備考のいずれかを入力してください。'])->withInput();
    }
        
        $food = Food::create([
        'people_id' => $request->people_id,
        'lunch' => $request->lunch,
        'lunch_bikou' => $request->lunch_bikou,
        'oyatsu' => $request->oyatsu,
        'oyatsu_bikou' => $request->oyatsu_bikou,
        'staple_food' => $request->staple_food,
        'side_dish' => $request->side_dish,
        'medicine' => $request->medicine,
        'medicine_name' => $request->medicine_name,
        'bikou' => $request->bikou,
         
    ]);
    // return redirect('people/{id}/edit');
    $people = Person::all();
    $request->session()->regenerateToken();
    // return view('people', compact('food', 'people'));
    return redirect()->route('people.index')->with('success', '登録完了しました。');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Food  $food
     * @return \Illuminate\Http\Response
     */
//     public function showFood($id)
// {
    
//     $person = Person::findOrFail($id);
//     $foods = $person->foods;
//     return view('people', compact('foods'));
// }



public function change(Request $request, $people_id, $id)
// public function change(Food $food)
    {
        
        $person = Person::findOrFail($people_id);
        $food = Food::findOrFail($id);
      
       
        return view('foodchange', compact('person', 'food'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Food  $food
     * @return \Illuminate\Http\Response
     */
//     public function edit(Request $request, $people_id)
// {
//     $person = Person::findOrFail($people_id);
//     return view('food', ['id' => $person->id],compact('person'));
// }
public function edit(Request $request, $people_id)
{
    $person = Person::findOrFail($people_id);
    $today = \Carbon\Carbon::now()->toDateString();
    $selectedDate = $request->input('selected_date', Carbon::now()->toDateString());
    $selectedDateStart = Carbon::parse($selectedDate)->startOfDay();
    $selectedDateEnd = Carbon::parse($selectedDate)->endOfDay();

    $foodsOnSelectedDate = $person->foods->whereBetween('created_at', [$selectedDateStart, $selectedDateEnd]);
    return view('foodedit', compact('person', 'selectedDate', 'foodsOnSelectedDate'));
}



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Food  $food
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $people_id, $id)
{
    $food = Food::findOrFail($id);
    
    // データ更新
    $food->lunch = $request->lunch;
    $food->lunch_bikou = $request->lunch_bikou;
    $food->oyatsu = $request->oyatsu;
    $food->oyatsu_bikou = $request->oyatsu_bikou;
    $food->staple_food = $request->staple_food;
    $food->side_dish = $request->side_dish;
    $food->medicine = $request->medicine;
    $food->medicine_name = $request->medicine_name;
    $food->bikou = $request->bikou;
    
    $food->save();
    
    $people = Person::all();
    // return redirect()->route('people.show', $people_id)->with('success', '食事情報が更新されました。');
    return redirect()->route('people.index')->with('success', '食事情報が更新されました。');
}
    
 
  

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Food  $food
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       
        $food = Food::find($id);
    if ($food) {
        $food->delete();
    }
        return redirect()->route('people.index');
    }
}