<?php

namespace App\Http\Controllers;

use App\Models\Temperature;
use App\Models\Person;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TemperatureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     
     
    
    public function index()
    {
       $temperature = Temperature::all();
       return view('people',compact('temperature'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
     
     
   public function create(Request $request)
{
    $person = Temperature::findOrFail($request->people_id);
    return redirect()->route('temperature.edit', ['people_id' => $person->id]);
}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
{
    try {
        $storeData = $request->validate([
            'people_id' => 'required|exists:people,id',
            'temperature' => 'required|numeric|between:35,42', // Add range validation
            'bikou' => 'nullable|string',
            'created_at' => 'required|date_format:H:i',
        ], [
            'temperature.required' => '体温は入力必須です',
            'temperature.numeric' => '体温は数値で入力してください',
            'temperature.between' => '体温は35℃から42℃の間で入力してください',
            'created_at.required' => '体温計測時間は必須です',
        ]);
        
        $temperature = Temperature::create([
            'people_id' => $request->people_id,    
            'temperature' => $request->temperature,
            'bikou' => $request->bikou,
            'created_at' => $request->created_at,
        ]);

        $user = auth()->user();
        $facilities = $user->facility_staffs()->get();
        $firstFacility = $facilities->first();
        $people = $firstFacility->people_facilities()->get();
         
        // 二重送信防止
        $request->session()->regenerateToken();

        return redirect()->route('people.index')->with('success', '体温が正常に登録されました。');
    } catch (\Illuminate\Validation\ValidationException $e) {
        return redirect()->back()->withErrors($e->errors())->withInput();
    } catch (\Exception $e) {
        \Log::error('Temperature registration error: ' . $e->getMessage());
        return redirect()->back()->with('error', '体温の登録中にエラーが発生しました。もう一度お試しください。')->withInput();
    }
}
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\temperature  $temperature
     * @return \Illuminate\Http\Response
     */
    public function show($people_id)
{
    
    $person = Person::findOrFail($people_id);
    $temperature = $person->temperatures;

    
    // return view('people',compact('temperature'));
    return redirect()->route('people.index');
}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\temperature  $temperature
     * @return \Illuminate\Http\Response
     */
//     public function edit($id)
// {
//     $person = Person::findOrFail($request->people_id);
//     return view('temperature.edit', ['id' => $person->id],compact('person'));
// }
public function edit(Request $request, $people_id)
{
   
    $person = Person::findOrFail($people_id);
    $today = \Carbon\Carbon::now()->toDateString();
    $selectedDate = $request->input('selected_date', Carbon::now()->toDateString());
    $selectedDateStart = Carbon::parse($selectedDate)->startOfDay();
    $selectedDateEnd = Carbon::parse($selectedDate)->endOfDay();

    $temperaturesOnSelectedDate = $person->temperatures->whereBetween('created_at', [$selectedDateStart, $selectedDateEnd]);
    return view('temperatureedit', compact('person', 'selectedDate', 'temperaturesOnSelectedDate'));
}


public function change(Request $request, $people_id, $id)
    {
        // ユーザーを取得
        $user = User::findOrFail($people_id);
        // ユーザーが持つ体温の記録からユーザーIDを取得
        $user_id = $user->id;
    
        // すべてのユーザーを取得
        $users = User::all();
        $person = Person::findOrFail($people_id);
        $temperature = Temperature::findOrFail($id);
        return view('temperaturechange', compact('person', 'temperature','users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\temperature  $temperature
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Temperature $temperature)
    {
    //データ更新
        $temperature = Temperature::find($request->id);
        $form = $request->all();
        $temperature->fill($form)->save();
    
        $request->session()->regenerateToken();
        $user = auth()->user();
        //  dd($user);
        // facility_staffsメソッドからuserの情報をゲットする↓
        $facilities = $user->facility_staffs()->get();
        // dd($facilities);
        $firstFacility = $facilities->first();
    
        // dd($firstFacility);
        // ↑これで$facilityが取れている
        $people= $firstFacility->people_facilities()->get();
        // $people = Person::all();
        // 二重送信防止
        $request->session()->regenerateToken();
        // return view('people', compact('temperature', 'people'));
        return redirect()->route('people.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\temperature  $temperature
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       
        $temperature = Temperature::find($id);
    if ($temperature) {
        $temperature->delete();
    }
        return redirect()->route('people.index');
    }
}