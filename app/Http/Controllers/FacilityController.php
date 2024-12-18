<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\MedicalCareNeed;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Spatie\Permission\Traits\HasRoles;
use App\Enums\RoleType as RoleEnum;

class FacilityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     
    // 事業所登録画面を表示
    public function create()
    {
        return view('facilityregister');

    }
   

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       // バリデーション
    $storeData = $request->validate([
        'facility_key' => 'required|string|max:10|regex:/^[a-zA-Z0-9]{1,10}$/|unique:facilities,facility_key',
        'facility_name' => 'required|string|max:255',
    ], [
        'facility_key.regex' => '施設IDは1から10桁までのアルファベットまたは数字でなければなりません。',
        'facility_key.unique' => 'この施設IDは既に登録されています。',
    ]);

    // 事業所を作成
    $facility = Facility::create([
        'facility_key' => $request->facility_key,
        'facility_name' => $request->facility_name,
    ]);
    
    // 中間テーブルへの登録
    // ログインしているユーザーのIDを取得して、関連付ける
    $user = auth()->user();
    $facility->facility_staffs()->attach($user->id);
    
    
    // $user->user_roles()->attach(1); // ここでrole_id＝1（staff）を紐づける
    $user->assignRole('facility staff administrator'); // ここで'facility staff administrator' を紐づける
    // 二重送信防止
    $request->session()->regenerateToken();
    return redirect('/medical_care_needs'); 
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Food  $food
     * @return \Illuminate\Http\Response
     */

     public function showMedicalCare()
{
    $user = Auth::user();
    $facility = $user->facility_staffs()->first();

    if (!$facility) {
        return redirect()->route('dashboard')->with('error', '所属する施設が見つかりません。');
    }

    $options = [
        'medical_care_majority' => 'はい',
        'medical_care_minority' => '少数だがいる',
        'no_medical_care' => 'いない'
    ];

    // 施設に関連付けられた医療的ケアニーズを取得
    $selectedItem = $facility->medicalCareNeeds()->first();
    $selectedValue = $selectedItem ? $selectedItem->name : 'no_medical_care';

    return view('medical_care_needs', compact('facility', 'options', 'selectedValue'));
}

    public function updateMedicalCareNeeds(Request $request)
{
    $facilityId = $request->input('facility_id');
    $medicalCareStatus = $request->input('medical_care_need_id');

    $facility = Facility::findOrFail($facilityId);

    // medical_care_needsテーブルから対応するIDを取得
    $medicalCareNeed = MedicalCareNeed::where('name', $medicalCareStatus)->first();

    if ($medicalCareNeed) {
        // 既存の関連をすべて削除し、新しい関連を追加
        $facility->medicalCareNeeds()->sync([$medicalCareNeed->id]);
    } else {
        // 選択されたステータスに対応するMedicalCareNeedが存在しない場合、
        // すべての関連を削除
        $facility->medicalCareNeeds()->detach();
    }

    return redirect()->route('people.index')->with('success', '更新されました。');
}
}
