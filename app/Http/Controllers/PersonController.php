<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Facility;
use App\Models\Person;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Chat;
use App\Models\Option;
use App\Models\OptionItem;
use App\Models\ScheduledVisit;
use App\Models\HogoshaText;

use Spatie\Permission\Models\Role as SpatieRole;
use App\Enums\RoleType;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;
use App\Enums\PermissionType;
use App\Enums\RoleType as RoleEnums;
use App\Enums\Role as RoleEnum;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PersonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
     public function index(User $user)
     {
         // ログインしているユーザーの情報↓
        $user = auth()->user();

        $user->facility_staffs()->first();

        // facility_staffsメソッドからuserの情報をゲットする↓
        $facilities = $user->facility_staffs()->get();
        $facilityIds = $facilities->pluck('id')->toArray();

        $roles = $user->user_roles()->get(); // これでロールが取得できる

        $rolename = $user->getRoleNames(); // ロールの名前を取得
        $isSuperAdmin = $user->hasRole(RoleType::FacilityStaffAdministrator);

        // ロールのIDを取得する場合
        $roleIds = $user->roles->pluck('id');

        $firstFacility = $facilities->first();
        if ($firstFacility) {
            // リレーションを事前にロード
            $people = Person::with([
                    'people_facilities', 
                    'scheduled_visits',
                    'times',
                    // 'temperatures',
                    // 'creatives',
                    // 'activities',
                    // 'trainings',
                    // 'lifestyles',
                    // 'foods',
                    // 'bloodpressures',
                    // 'toilets',
                    // 'waters',
                    // 'medicines',
                    // 'tubes',
                    // 'kyuuins',
                    // 'hossas',
                    // 'speeches',
                    // 'notebooks',
                
                ])
                ->whereHas('people_facilities', function ($query) use ($facilityIds) {
                    $query->whereIn('facilities.id', $facilityIds);
                })->get();

            // dd($people);
            // 本日の日付を取得
            $today = \Carbon\Carbon::now()->toDateString();
            // $people = $firstFacility->people_facilities()->get();

            // 本日訪問予定がある人物のみを取得(送迎は開発途中のためコメントアウト。一旦利用者全員表示させる)
            // $people = $firstFacility->people_facilities()
            // ->with('scheduled_visits') // リレーションを事前にロード
            //     ->whereHas('scheduled_visits', function($query) use ($today) {
            //         $query->whereDate('arrival_datetime', $today)
            //               ->orWhereDate('exit_datetime', $today);
            //     })
            //     ->get();
            // } else {
            //     $people = collect([]); // 空のコレクションにする
            // }

            foreach ($people as $person) {
                $unreadChats = Chat::where('people_id', $person->id)
                                    ->where('is_read', false)
                                    ->where('user_identifier', '!=', $user->id)
                                    ->exists();
            
                $person->unreadChats = $unreadChats;
                \Log::info("Person {$person->id} unread messages: " . ($unreadChats ? 'true' : 'false'));
        }

        foreach ($people as $person) {
            $unreadMessages = HogoshaText::where('people_id', $person->id)
                                ->where('is_read', false)
                                ->where('user_identifier', '!=', $user->id)
                                ->exists();
        
            $person->unreadMessages = $unreadMessages;
            \Log::info("Person {$person->id} unread messages: " . ($unreadMessages ? 'true' : 'false'));
    }

        $selectedItems = [];
        
        foreach ($people as $person) {
            $selectedItems[$person->id] = json_decode($person->selected_items, true) ?? [];
        }
        
        $today = \Carbon\Carbon::now()->toDateString();

        foreach ($people as $person) {
            $person->todayOptionItems = OptionItem::where('people_id', $person->id)
                ->whereDate('created_at', $today)
                ->get();
        }
    
       // optionsテーブルから必要なデータを取得
       $options = Option::whereIn('people_id', $people->pluck('id'))
        ->get(['id', 'people_id', 'title', 'item1', 'item2', 'item3', 'item4', 'item5']);
        $personOptions = [];
        foreach ($people as $person) {
            $personOptions[$person->id] = Option::where('people_id', $person->id)
                ->where('flag', 1)
                ->get();
        }

        // 各利用者の訪問データを取得して送迎の要否を確認(送迎は開発途中のためコメントアウト）
        // foreach ($people as $person) {
        //     $scheduledVisit = ScheduledVisit::where('people_id', $person->id)->first();
        //     $person->transport = $scheduledVisit ? $scheduledVisit->transport : '未登録';
        // }

    return view('people', compact('people', 'selectedItems', 'options', 'personOptions','unreadChats'));
    }
    else {
        // $people = collect([]); // 空のコレクションを作成
        return redirect()->route('before-login')->withErrors(['access_denied' => 'アクセス権限がありません。']);
}
}

public function getContent($id)
    {
        $person = Person::findOrFail($id);
        // ログインしているユーザーの情報↓
        $user = auth()->user();

        $user->facility_staffs()->first();

        // facility_staffsメソッドからuserの情報をゲットする↓
        $facilities = $user->facility_staffs()->get();
        $facilityIds = $facilities->pluck('id')->toArray();

        $roles = $user->user_roles()->get(); // これでロールが取得できる

        $rolename = $user->getRoleNames(); // ロールの名前を取得
        $isSuperAdmin = $user->hasRole(RoleType::FacilityStaffAdministrator);

        // ロールのIDを取得する場合
        $roleIds = $user->roles->pluck('id');

        $firstFacility = $facilities->first();
        $people = Person::with([
            'people_facilities', 
            'scheduled_visits',
            'times',
            // 'temperatures',
            // 'creatives',
            // 'activities',
            // 'trainings',
            // 'lifestyles',
            // 'foods',
            // 'bloodpressures',
            // 'toilets',
            // 'waters',
            // 'medicines',
            // 'tubes',
            // 'kyuuins',
            // 'hossas',
            // 'speeches',
            // 'notebooks',
        
        ])
        ->whereHas('people_facilities', function ($query) use ($facilityIds) {
            $query->whereIn('facilities.id', $facilityIds);
        })->get();
            $unreadMessages = HogoshaText::where('people_id', $person->id)
                                ->where('is_read', false)
                                ->where('user_identifier', '!=', $user->id)
                                ->exists();
        
            $person->unreadMessages = $unreadMessages;
            \Log::info("Person {$person->id} unread messages: " . ($unreadMessages ? 'true' : 'false'));
 

        $selectedItems = [];
        
    
            $selectedItems[$person->id] = json_decode($person->selected_items, true) ?? [];

        
        $today = \Carbon\Carbon::now()->toDateString();

            $person->todayOptionItems = OptionItem::where('people_id', $person->id)
                ->whereDate('created_at', $today)
                ->get();

    
       // optionsテーブルから必要なデータを取得
       $options = Option::whereIn('people_id', $people->pluck('id'))
        ->get(['id', 'people_id', 'title', 'item1', 'item2', 'item3', 'item4', 'item5']);
        $personOptions = [];

            $personOptions[$person->id] = Option::where('people_id', $person->id)
                ->where('flag', 1)
                ->get();

                return view('partials._people_content',compact('people', 'person','selectedItems', 'options', 'personOptions'));
    }

    public function show($id)
{
    $person = Person::findOrFail($id);
        $user = auth()->user();

    $user->facility_staffs()->first();

    // facility_staffsメソッドからuserの情報をゲットする↓
    $facilities = $user->facility_staffs()->get();

    $roles = $user->user_roles()->get(); // これでロールが取得できる

    $rolename = $user->getRoleNames(); // ロールの名前を取得
    $isSuperAdmin = $user->hasRole(RoleType::FacilityStaffAdministrator);

    // ロールのIDを取得する場合
    $roleIds = $user->roles->pluck('id');

    $firstFacility = $facilities->first();
    if ($firstFacility) {
        $people = $firstFacility->people_facilities()->get();
    } else {
        $people = []; // まだpeople（利用者が登録されていない時もエラーが出ないようにする）
    }

    foreach ($people as $person) {
        $unreadMessages = Chat::where('people_id', $person->id)
                              ->where('is_read', false)
                              ->where('user_identifier', '!=', $user->id)
                              ->exists();
    
        $person->unreadMessages = $unreadMessages;
        \Log::info("Person {$person->id} unread messages: " . ($unreadMessages ? 'true' : 'false'));
    }

    $selectedItems = [];
        
        // Loop through each person and decode their selected items
        foreach ($people as $person) {
            $selectedItems[$person->id] = json_decode($person->selected_items, true) ?? [];
        }


    // options テーブルから追加項目を取得
    $options = Option::whereIn('people_id', $people->pluck('id'))
        ->get(['title', 'item1', 'item2', 'item3', 'item4', 'item5']);
        $selectedItems = [];
    
        $today = \Carbon\Carbon::now()->toDateString();

        foreach ($people as $person) {
            $person->todayOptionItems = OptionItem::where('people_id', $person->id)
                ->whereDate('created_at', $today)
                ->get();
        }
    
       // optionsテーブルから必要なデータを取得
       $options = Option::whereIn('people_id', $people->pluck('id'))
        ->get(['id', 'people_id', 'title', 'item1', 'item2', 'item3', 'item4', 'item5']);

        $personOptions = [];
        foreach ($people as $person) {
            $personOptions[$person->id] = Option::where('people_id', $person->id)
                ->where('flag', 1)
                ->get();
    }

    // return view('people', compact('people', 'selectedItems', 'personOptions', 'options', 'id'));
    return redirect()->route('people.index');
}

    /**
     * Show the form for creating a new resource.
     *
 
     */
    // 利用者全員の一覧（peoplelistビュー）
    public function list()
{
    $user = auth()->user();
    $facilities = $user->facility_staffs()->get();
    $firstFacility = $facilities->first();

    // Retrieve people associated with the first facility
    if ($firstFacility) {
        $people = $firstFacility->people_facilities()->get();
    } else {
        $people = []; // Handle case when no people are registered
    }

    return view('peoplelist', compact('people'));
}
    public function create()
    {
        $user = Auth::user();
        $facility = $user->facility_staffs()->first();
   
        return view('peopleregister', compact('facility'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name_kana' => ['required', 'string', 'regex:/^[ァ-ヶー]+$/u'],
            'first_name_kana' => ['required', 'string', 'regex:/^[ァ-ヶー]+$/u'],
            'date_of_birth' => 'required|date',
            'jukyuusha_number' => 'required|digits:10',
            'filename' => 'nullable|image|max:2048',
        ], [
            'last_name.required' => '姓は必須項目です。',
            'first_name.required' => '名は必須項目です。',
            'last_name_kana.required' => 'セイは必須項目です。',
            'last_name_kana.regex' => 'セイはカタカナのみで入力してください。',
            'first_name_kana.required' => 'メイは必須項目です。',
            'first_name_kana.regex' => 'メイはカタカナのみで入力してください。',
            'date_of_birth.required' => '生年月日は必須項目です。',
            'date_of_birth.date' => '正しい日付形式で入力してください。',
            'jukyuusha_number.required' => '受給者証番号は必須項目です。',
            'jukyuusha_number.digits' => '受給者証番号は10桁の数字で入力してください。',
            'filename.image' => '画像ファイルを選択してください。',
            'filename.max' => '画像ファイルは2MB以下にしてください。',
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()
                             ->withErrors($validator)
                             ->withInput();
        }
    
        $user = auth()->user();
        $facilities = $user->facility_staffs()->get();
        $firstFacility = $facilities->first();
    
        if (!$firstFacility) {
            return redirect()->back()->withErrors(['facility' => '施設が見つかりません。']);
        }
    
            
            // 名前と生年月日が一致する利用者を検索
        $existingPersonByNameAndDob = $firstFacility->people_facilities()
            ->where('last_name', $request->last_name)
            ->where('first_name', $request->first_name)
            ->where('date_of_birth', $request->date_of_birth)
            ->first();

        // 受給者番号が一致する利用者を検索
        $existingPersonByJukyuushaNumber = $firstFacility->people_facilities()
            ->where('jukyuusha_number', $request->jukyuusha_number)
            ->first();
            // 名前と生年月日が一致する場合
        if ($existingPersonByNameAndDob) {
            return back()->withInput($request->all())
                         ->withErrors(['duplicate_name_dob' => '同じ名前と生年月日の人がすでに存在します。']);
        }

        // 受給者番号が一致する場合
        if ($existingPersonByJukyuushaNumber) {
            return back()->withInput($request->all())
                         ->withErrors(['duplicate_jukyuusha_number' => '同じ受給者番号の人がすでに存在します。']);
        }
    
   
        

        $directory = 'sample/person_photo';
        $filename = null;
        $filepath = null;

        if ($request->hasFile('filename')) {
            $request->validate([
                'filename' => 'image|max:2048',
            ]);
            $filename = uniqid() . '.' . $request->file('filename')->getClientOriginalExtension();
            $request->file('filename')->storeAs($directory, $filename);
            $filepath = $directory . '/' . $filename;
        }

        $newpeople = Person::create([
            'last_name' => $request->last_name,
            'first_name' => $request->first_name,
            'last_name_kana' => $request->last_name_kana,
            'first_name_kana' => $request->first_name_kana,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'jukyuusha_number' => $request->jukyuusha_number,
            'medical_care' => $request->medical_care,
            'filename' => $filename,
            'path' => $filepath,

        ]);
        


        // 現在ログインしているユーザーが属する施設にpeople（利用者）を紐づける↓
        // syncWithoutDetaching＝完全重複以外は、重複OK
        $newpeople->people_facilities()->syncWithoutDetaching($firstFacility->id);
         // 既存の記録項目を新規利用者に紐づける
        $this->linkExistingOptionsToNewPerson($newpeople, $firstFacility);

        if ($firstFacility) {
            $people = $firstFacility->people_facilities()->get();
        } else {
            $people = []; // まだpeople（利用者が登録されていない時もエラーが出ないようにする）
        }

        // 二重送信防止
        $request->session()->regenerateToken();
        // return view('people', compact('people'));
        return redirect()->route('people.index');
    }

    private function linkExistingOptionsToNewPerson(Person $newPerson, Facility $facility)
{
    // 施設の既存の記録項目グループを取得（NULLでないもののみ）
    $existingOptionGroups = Option::where('facility_id', $facility->id)
        ->whereNotNull('option_group_id')
        ->select('option_group_id')
        ->distinct()
        ->get();

    foreach ($existingOptionGroups as $group) {
        // グループ内の最初の記録項目を取得
        $sampleOption = Option::where('option_group_id', $group->option_group_id)
            ->first();

        if ($sampleOption) {
            // 新しい利用者用の記録項目を作成
            $newOption = $sampleOption->replicate();
            $newOption->people_id = $newPerson->id;
            // flagが1の場合は、新しいオプションでもflagを1に設定
            if ($sampleOption->flag == 1) {
                $newOption->flag = 1;
                }
            $newOption->save();
        }
    }
}


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Person  $person
     * @return \Illuminate\Http\Response
     */
   


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Person  $person
     * @return \Illuminate\Http\Response
     */

   // 利用者情報更新画面の表示↓
   public function edit($id)
   {
       
    $user = Auth::user();
    $person = Person::findOrFail($id);
    $facility = $user->facility_staffs()->first();
    
    $people = $user->people_family()->get();
    
    // Generate URL using the current person's ID if there are no family members
    $encryptedId = Crypt::encryptString($person->id);
    $url = URL::temporarySignedRoute(
        'terms.show', 
        now()->addHours(24), 
        ['encrypted_id' => $encryptedId]
    );
    
    \Log::info('Generated URL: ' . $url);
    \Log::info('Person ID: ' . $person->id);
    \Log::info('People family count: ' . $people->count());

    $qrCode = QrCode::size(200)->generate($url);
    $firstFacility = $facility->first();

    // Retrieve people associated with the first facility
    if ($firstFacility) {
        $facilitypeople = $firstFacility->people_facilities()->get();
    } else {
        $facilitypeople = []; // Handle case when no people are registered
    }

    // Add this line to check if $facilitypeople has data
    \Log::info('Facility people count: ' . $facilitypeople->count());

    return view('peopleedit', compact('url', 'qrCode', 'person', 'facility', 'facilitypeople'));
}





    //  利用者情報更新
    public function update(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'last_name' => 'required|string|max:255',
        'first_name' => 'required|string|max:255',
        'last_name_kana' => ['required', 'string', 'regex:/^[ァ-ヶー]+$/u'],
        'first_name_kana' => ['required', 'string', 'regex:/^[ァ-ヶー]+$/u'],
        'date_of_birth' => 'required|date',
        'jukyuusha_number' => [
            'required',
            'digits:10',
            Rule::unique('people', 'jukyuusha_number')->ignore($id), // 現在の利用者を除外
        ],
        'filename' => 'nullable|image|max:2048',
    ], [
        'last_name.required' => '姓は必須項目です。',
        'first_name.required' => '名は必須項目です。',
        'last_name_kana.required' => 'セイは必須項目です。',
        'last_name_kana.regex' => 'セイはカタカナのみで入力してください。',
        'first_name_kana.required' => 'メイは必須項目です。',
        'first_name_kana.regex' => 'メイはカタカナのみで入力してください。',
        'date_of_birth.required' => '生年月日は必須項目です。',
        'date_of_birth.date' => '正しい日付形式で入力してください。',
        'jukyuusha_number.required' => '受給者証番号は必須項目です。',
        'jukyuusha_number.digits' => '受給者証番号は10桁の数字で入力してください。',
        'jukyuusha_number.unique' => 'この受給者証番号は既に登録されています。',
        'filename.image' => '画像ファイルを選択してください。',
        'filename.max' => '画像ファイルは2MB以下にしてください。',
    ]);
    if ($validator->fails()) {
        $errors = $validator->errors();
        
        // 受給者証番号のエラーメッセージを調整
        if ($errors->has('jukyuusha_number')) {
            $jukyuushaErrors = $errors->get('jukyuusha_number');
            if (in_array('この受給者証番号は既に登録されています。', $jukyuushaErrors)) {
                // 新しいMessageBagインスタンスを作成し、必要なエラーメッセージのみを追加
                $newErrors = new MessageBag();
                foreach ($errors->messages() as $key => $messages) {
                    if ($key !== 'jukyuusha_number') {
                        $newErrors->add($key, $messages[0]);
                    }
                }
                $newErrors->add('jukyuusha_number', 'この受給者証番号は既に登録されています。');
                $errors = $newErrors;
            }
        }
    
        return redirect()->back()
                         ->withErrors($errors)
                         ->withInput();
    }

    $user = auth()->user();
    $facilities = $user->facility_staffs()->get();
    $firstFacility = $facilities->first();

    if (!$firstFacility) {
        return back()->withErrors(['error' => '施設情報が見つかりません。']);
    }
        $person = Person::findOrFail($id);

        // 名前と生年月日が一致する利用者を検索（現在の利用者を除く）
        $existingPersonByNameAndDob = $firstFacility->people_facilities()
            ->where('people.id', '!=', $id)
            ->where('last_name', $request->last_name)
            ->where('first_name', $request->first_name)
            ->where('date_of_birth', $request->date_of_birth)
            ->first();

        // 名前と生年月日が一致する場合
        if ($existingPersonByNameAndDob) {
            return back()->withInput($request->all())
                         ->withErrors(['duplicate_name_dob' => '同じ名前と生年月日の人がすでに存在します。']);
        }

        // 受給者番号が一致する利用者を検索（現在の利用者を除く）
$existingPersonByJukyuushaNumber = $firstFacility->people_facilities()
->where('people.id', '!=', $id)
->where('jukyuusha_number', $request->jukyuusha_number)
->exists(); // データの存在のみ確認

// 受給者番号が一致する場合
if ($existingPersonByJukyuushaNumber) {
\Log::info('同じ受給者番号の利用者が存在:', ['jukyuusha_number' => $request->jukyuusha_number]);
return back()->withInput($request->all())
             ->withErrors(['duplicate_jukyuusha_number' => '同じ受給者番号の人がすでに存在します。']);
}


        //   画像保存
        $directory = 'sample/person_photo';
        $filename = $person->filename; // 更新しない場合既存のファイル名を保持
        $filepath = $person->path; // 既存のパスを保持
    
        if ($request->hasFile('filename')) {
            $request->validate([
                'filename' => 'image|max:2048',
            ]);

        // 古い画像ファイルが存在する場合は削除
        if ($person->path && \Storage::exists($person->path)) {
            \Storage::delete($person->path);
        }   
            // 同じファイル名でも上書きされないようユニークなIDをファイル名に追加
            $uniqueId = uniqid();
            $originalFilename = $request->file('filename')->getClientOriginalName();
            $filename = $uniqueId . '_' . $originalFilename;
            $request->file('filename')->storeAs($directory, $filename);
            $filepath = $directory . '/' . $filename;
            
        }
        // バリデーションした内容を保存する↓
        
        

        $person->update([
            'last_name' => $request->last_name,
            'first_name' => $request->first_name,
            'last_name_kana' => $request->last_name_kana,
            'first_name_kana' => $request->first_name_kana,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'jukyuusha_number' => $request->jukyuusha_number,
            'medical_care' => $request->medical_care,
            'filename' => $filename,
            'path' => $filepath,
        ]);
        $people = Person::all();
        // 二重送信防止
        $request->session()->regenerateToken();

        // ログインしているユーザーの情報↓
        $user = auth()->user();

        $user->facility_staffs()->first();

        // facility_staffsメソッドからuserの情報をゲットする↓
        $facilities = $user->facility_staffs()->get();

        // dd($facilities);
        $roles = $user->user_roles()->get(); // これでロールが取得できる
        //   dd($roles);

        $rolename = $user->getRoleNames(); // ロールの名前を取得

        $isSuperAdmin = $user->hasRole(RoleType::FacilityStaffAdministrator);

        // ロールのIDを取得する場合
        $roleIds = $user->roles->pluck('id');

        $firstFacility = $facilities->first();
        if ($firstFacility) {
            $people = $firstFacility->people_facilities()->get();
        } else {
            $people = []; // まだpeople（利用者が登録されていない時もエラーが出ないようにする）
        }

        foreach ($people as $person) {
            $unreadMessages = Chat::where('people_id', $person->id)
                                  ->where('is_read', false)
                                  ->where('user_identifier', '!=', $user->id)
                                  ->exists();
        
            $person->unreadMessages = $unreadMessages;
            \Log::info("Person {$person->id} unread messages: " . ($unreadMessages ? 'true' : 'false'));
        }

        $selectedItems = [];
        
        // Loop through each person and decode their selected items
        foreach ($people as $person) {
            $selectedItems[$person->id] = json_decode($person->selected_items, true) ?? [];
        }

        // return view('people', compact('people', 'selectedItems'))->with('success', '利用者情報が更新されました。');
        return redirect()->route('people.index')->with('success', '利用者情報が更新されました。');
    }


    // 登録項目の選択↓
    public function showSelectedItems($people_id, $id)
{
    $person = Person::findOrFail($id);
    $facility = $person->people_facilities()->first();
    $selectedItems = json_decode($person->selected_items, true) ?? [];
    
    $options = Option::where('people_id', $id)->get();
    
    $additionalItems = $options->map(function ($option) {
        return [
            'id' => $option->id,
            'title' => $option->title,
            'items' => $option->getItemsAsString(),
            'flag' => $option->flag,
            'option_group_id' => $option->option_group_id ?? null,
        ];
    })->toArray();

    $facilityItems = array_filter($additionalItems, function($item) {
        return $item['option_group_id'] !== null;
    });

    $individualItems = array_filter($additionalItems, function($item) {
        return $item['option_group_id'] === null;
    });

    return view('select_item', compact('person', 'facility', 'additionalItems' ,'selectedItems', 'facilityItems', 'individualItems', 'id'));
}

public function updateSelectedItems(Request $request, $id)
{
    $person = Person::findOrFail($id);
    $selectedOptions = $request->input('selected_options', []); // チェックされたオプション項目を取得
    $selectedFixedItems = $request->input('selected_fixed_items', []); // チェックされた固定項目を取得

    // 施設全体に紐づく記録項目以外の個人の記録項目を取得し、フラグを更新
    $options = Option::where('people_id', $id)
    ->whereNull('option_group_id')
    ->get();

    foreach ($options as $option) {
        $option->flag = in_array($option->id, $selectedOptions) ? 1 : 0;
        $option->save();
    }

    // 選択された項目のデータを準備（オプションと固定項目の両方）
    $selectedItemsData = array_merge(
        Option::whereIn('id', $selectedOptions)->select('id', 'title')->get()
            ->map(function($item) {
                return ['id' => $item->id, 'title' => $item->title, 'fixed' => false];
            })->toArray(),
        array_map(function($item) {
            return ['id' => 'fixed_' . $item, 'title' => $item, 'fixed' => true];
        }, $selectedFixedItems)
    );
    // dd($selectedItemsData);

    // JSON 形式で保存
    $person->selected_items = json_encode($selectedItemsData, JSON_UNESCAPED_UNICODE);
    $person->save();
    $savedPerson = Person::find($id);
    // dd('保存後のデータ:', json_decode($savedPerson->selected_items, true));

    return redirect()->route('people.index', $person->id)->with('success', '記録項目が更新されました。');
}











// 新しく項目を追加するメソッド
private function getAdditionalItems($id)
{
    $person = Person::findOrFail($id);
    $options = Option::where('people_id', $id)
                    ->orderBy('created_at', 'desc')  // 新しく追加された項目を上に表示
                    ->get();

    $additionalItems = [];
    foreach ($options as $option) {
        $items = [];
        for ($i = 1; $i <= 5; $i++) {
            $itemKey = "item{$i}";
            if (!is_null($option->$itemKey) && $option->$itemKey !== '') {
                $items[] = $option->$itemKey;
            }
        }
        
        $additionalItems[] = [
            'id' => $option->id,
            'title' => $option->title,
            'items' => implode(', ', $items),
            'facility_id' => $option->facility_id,
            'is_new' => $option->created_at->gt(now()->subMinutes(5))  // 5分以内に作成された項目を新規とみなす
        ];
    }

    return $additionalItems;
}

public function showAddItemForm($id)
{
    $user = Auth::user();
    $facility = $user->facility_staffs()->findOrFail($id);
    $facilityIds = [$facility->id];

    // 施設に関連する全てのオプションを取得
    $options = Option::whereIn('facility_id', $facilityIds)
                     ->orderBy('option_group_id')
                     ->get()
                     ->groupBy('option_group_id');
    
    $additionalItems = $options->map(function ($groupedOptions, $groupId) {
        $firstOption = $groupedOptions->first();
        return [
            'id' => $firstOption->id,
            'group_id' => $groupId,
            'title' => $firstOption->title,
            'items' => $firstOption->getItemsAsString(),
            'flag' => $firstOption->flag,
            'facility_id' => $firstOption->facility_id
        ];
    })->values()->toArray();

    // デフォルトで全ての項目を選択状態にする
    $selectedItems = $options->flatten()->pluck('id')->toArray();

    return view('item', compact('facility','id' ,'additionalItems', 'selectedItems'));
}





public function addItemToAll($people_id)
{
    try {
        $itemId = $request->input('item_id');
        // itemIdに基づいてOptionを取得
    $option = Option::find($itemId);
    
    // タイトルが存在する場合は取得し、存在しない場合はnullを返す
    $itemTitle = $option ? $option->title : null;

        dd($request->all());

        $facilityId = $request->input('facility_id');
        dd($facilityId);
        $people = Person::where('facility_id', $facilityId)->get();

        foreach ($people as $person) {
            $selectedItems = json_decode($person->selected_items, true) ?? [];
            if (!in_array($itemId, $selectedItems)) {
                $selectedItems[] = $itemId;
                $person->selected_items = json_encode(array_unique($selectedItems), JSON_UNESCAPED_UNICODE);
                $person->save();
            }
        }
        $request->session()->regenerateToken();
        return response()->json(['message' => '項目が全ての利用者に追加されました。']);
    } catch (\Exception $e) {
        \Log::error('Error in addItemToAll: ' . $e->getMessage());
        return response()->json(['error' => 'エラーが発生しました。再度お試しください。'], 500);
    }
}

public function updateFacilityItems(Request $request, $facility_id)
{
    // バリデーションルールとカスタムメッセージを設定
    $request->validate([
        'title' => 'required|string|max:32',
        'item.0' => 'required|string|max:32',
    ], [
        'title.required' => 'タイトルを入れてください。',
        'item.0.required' => '記録項目を入れてください。',
    ]);

    $selectedItems = $request->input('selected_items', []);
    
    // 施設に関連する全てのオプションを取得し、グループ化する
    $facilityOptions = Option::where('facility_id', $facility_id)
        ->get()
        ->groupBy('option_group_id');
    
    foreach ($facilityOptions as $groupId => $options) {
        $isSelected = in_array($options->first()->id, $selectedItems);
        
        foreach ($options as $option) {
            $option->flag = $isSelected ? 1 : 0;
            $option->save();
        }
    }

    return redirect()->back()->with('success', '記録項目が更新されました。');
}


    public function uploadForm()
    {
        // return view('people');変更↓
        return view('peopleregister');
    }





    public function __invoke()
    {
        return view('person');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Person  $person
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       
        $person = Person::find($id);
         // 手動で関連するoptionsレコードを削除
        DB::table('options')->where('people_id', $person->id)->delete();
        $person->delete();
        return redirect()->route('people.index')->with('success', '利用者の削除が完了しました。');
       
    
    }
}