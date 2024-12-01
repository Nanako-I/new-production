<?php

namespace App\Http\Controllers;

use App\Models\Notebook;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Time;
use App\Models\Temperature;
use App\Models\Bloodpressure;
use App\Models\Toilet;
use App\Models\Food;
use App\Models\Water;
use App\Models\Medicine;
use App\Models\Kyuuin;
use App\Models\Tube;
use App\Models\Hossa;
use App\Models\Speech;
use App\Models\Activity;
use App\Models\Training;
use App\Models\Lifestyle;
use App\Models\Creative;
use App\Models\Record;
use App\Models\Option;
use App\Models\OptionItem;

class NotebookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $notebook = Notebook::all();
        // ('people')に$peopleが代入される
        
        // 'people'はpeople.blade.phpの省略↓　// compact('people')で合っている↓
        return view('people',compact('notebook'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function create(Request $request)
    {
        $storeData = $request->validate([
            // 'temperature' => 'required|max:255',
            // 'people_id' => 'required|exists:people,id',
        ]);
        // バリデーションした内容を保存する↓
        
        $notebook = Notebook::create([
        'people_id' => $request->people_id,
        'notebook' => $request->notebook,
        
        
    ]);
    // return redirect('people/{id}/edit');
//   $person = Person::findOrFail($request->people_id);
   $people = Person::all();
    // return redirect()->route('speech.edit', ['people_id' => $person->id]);
    // return view('people', ['people' => Person::all()]);
    return view('people', compact('notebook', 'people'));
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
            // 'temperature' => 'required|max:255',
            // 'people_id' => 'required|exists:people,id',
        ]);
        // バリデーションした内容を保存する↓
        
        $notebook = Notebook::create([
        'people_id' => $request->people_id,
        'notebook' => $request->notebook,
        
    ]);
   $people = Person::all();
   $request->session()->regenerateToken();
   return redirect()->route('people.index')->with('success', '文章が正常に登録されました。');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Speech  $speech
     * @return \Illuminate\Http\Response
     */
    public function show($people_id)
{
    

    $person = Person::findOrFail($people_id);
    // $this->authorize('view', $person);
    $notebooks = $person->notebooks;

    $today = \Carbon\Carbon::now()->toDateString();
    $selectedDate = request()->input('selected_date', \Carbon\Carbon::now()->toDateString());
    $selectedDateStart = \Carbon\Carbon::parse($selectedDate)->startOfDay();    
    $selectedDateEnd = \Carbon\Carbon::parse($selectedDate)->endOfDay();
    
     // 選択された日付に該当する記録をすべて取得
     $records = Record::where('person_id', $people_id)
     ->whereBetween('kiroku_date', [$selectedDateStart, $selectedDateEnd])
     ->get();

     $timesOnSelectedDate = $person->times ? $person->times->whereBetween('created_at', [$selectedDateStart, $selectedDateEnd]) : collect();

    $foodsOnSelectedDate = $person->foods->whereBetween('created_at', [$selectedDateStart, $selectedDateEnd]);
    $foodString = '';
    $foodCount = count($foodsOnSelectedDate);
    
    $temperaturesOnSelectedDate = $person->temperatures ? $person->temperatures->whereBetween('created_at', [$selectedDateStart, $selectedDateEnd]) : collect();
    $temperatureString = '';
    $temperatureCount = count($temperaturesOnSelectedDate);
    

    $watersOnSelectedDate = $person->waters->whereBetween('created_at', [$selectedDateStart, $selectedDateEnd]);
    $medicinesOnSelectedDate = $person->medicines->whereBetween('created_at', [$selectedDateStart, $selectedDateEnd]);
    $tubesOnSelectedDate = $person->tubes->whereBetween('created_at', [$selectedDateStart, $selectedDateEnd]);
    
    

    $bloodpressuresOnSelectedDate = $person->bloodpressures->whereBetween('created_at', [$selectedDateStart, $selectedDateEnd]);
    $toiletsOnSelectedDate = $person->toilets->whereBetween('created_at', [$selectedDateStart, $selectedDateEnd]);
    $kyuuinsOnSelectedDate = $person->kyuuins ? $person->kyuuins->whereBetween('created_at', [$selectedDateStart, $selectedDateEnd]) : collect();
    $hossasOnSelectedDate = $person->hossas ? $person->hossas->whereBetween('created_at', [$selectedDateStart, $selectedDateEnd]) : collect();
    $speechesOnSelectedDate = $person->speeches ? $person->speeches->whereBetween('created_at', [$selectedDateStart, $selectedDateEnd]) : collect();

    // 選択された日付のオプションデータを取得
    // $optionsOnSelectedDate = $person->option_items ? $person->option_items->whereBetween('created_at', [$selectedDateStart, $selectedDateEnd]) : collect();
    // $lastOptions = OptionItem::where('people_id', $people_id)
    //     ->whereBetween('created_at', [$selectedDateStart, $selectedDateEnd])
    //     ->latest()
    //     ->first();
    // // 対応するOptionモデルのデータを取得 
    // $correspondingOption = null;
    // if ($lastOptions) {
    // $correspondingOption = Option::where('id', $lastOptions->option_id)->first();
    // }
    $optionItems = OptionItem::where('people_id', $people_id)
    ->whereBetween('created_at', [$selectedDateStart, $selectedDateEnd])
    ->get();

    $correspondingOptions = [];
    foreach ($optionItems as $optionItem) {
        $correspondingOptions[$optionItem->id] = Option::find($optionItem->option_id);
    }

    // hanamaruの項目↓
    $lastTime = Time::where('people_id', $people_id)
    ->whereDate('created_at', $selectedDate)
    ->latest()
    ->first();

    $lastMorningActivity = Speech::where('people_id', $people_id)
        ->whereDate('created_at', $selectedDate)
        ->whereNotNull('morning_activity')
        ->latest()
        ->first();

    $lastAfternoonActivity = Speech::where('people_id', $people_id)
    ->whereDate('created_at', $selectedDate)
    ->whereNotNull('afternoon_activity')
    ->latest()
    ->first();
    
    $lastActivity = Activity::where('people_id', $people_id)
        ->whereDate('created_at', $selectedDate)
        ->latest()
        ->first();
        
    $lastTraining = Training::where('people_id', $people_id)
        ->whereDate('created_at', $selectedDate)
        ->latest()
        ->first();
        
    $lastLifestyle = Lifestyle::where('people_id', $people_id)
        ->whereDate('created_at', $selectedDate)
        ->latest()
        ->first();
        
    $lastCreative = Creative::where('people_id', $people_id)
        ->whereDate('created_at', $selectedDate)
        ->latest()
        ->first();
    


    $url = 'https://acp-api.amivoice.com/issue_service_authorization';
    
    $apiID = config('services.amivoice.api_id');
    $apiPW = config('services.amivoice.api_pw');
    // dd($apiPW);
    $data = [
     'sid' => $apiID,//変数名＝値
     'spw' => $apiPW,
     'epi' => 300000,
    ];
    $queryString = http_build_query($data);
 
    $jsonData = json_encode($data);

    // dd($jsonData);
    $headers = [
        // 'Content-Type: application/json',
        'Authorization: Bearer ' . $jsonData
    ];

    
    $curl_handle = curl_init();//curlセッションを初期化して、curlハンドルを取得
    curl_setopt($curl_handle, CURLOPT_POST, TRUE);
    curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl_handle, CURLOPT_URL, $url);
    curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $queryString);
    curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $headers);

    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true); // curl_exec()の結果を文字列にする
    $json_response = curl_exec($curl_handle);
    if ($json_response === false) {
    echo 'Curl error: ' . curl_error($curl_handle);
} else {
}
    if(curl_exec($curl_handle) === false) {
    echo 'Curl error: ' . curl_error($curl_handle);
}
    
    curl_close($curl_handle);
  
    $people = Person::all();
    return view('notebookwriting', ['id' => $person->id],compact('person', 'json_response', 'selectedDate', 'records', 'timesOnSelectedDate','foodsOnSelectedDate','foodString','foodCount',  'watersOnSelectedDate' , 'medicinesOnSelectedDate', 'tubesOnSelectedDate',  'temperaturesOnSelectedDate', 'temperatureString', 'temperatureCount','bloodpressuresOnSelectedDate','toiletsOnSelectedDate','kyuuinsOnSelectedDate', 'hossasOnSelectedDate', 'speechesOnSelectedDate' , 'lastTime', 'lastMorningActivity', 'lastAfternoonActivity', 'lastActivity', 'lastTraining', 'lastLifestyle', 'lastCreative','optionItems', 'correspondingOptions'));
}

    
   



    public function getAmivoiceApiKey()
    {
        $amivoiceApiKey = Config::get('services.amivoice.api_key');
        return response()->json(['amivoiceApiKey' => $amivoiceApiKey]);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Speech  $speech
     * @return \Illuminate\Http\Response
     */
     public function edit($people_id)
{
    
}


public function change(Request $request, $people_id, $id)
// public function change(Food $food)
    {
        
        $person = Person::findOrFail($people_id);
        $lastNotebook = Notebook::findOrFail($id);
    
    // その日の最後のnotebookを取得
    // $lastNotebook = $person->notebooks()
    //     ->whereDate('created_at', now()->toDateString())
    //     ->latest()
    //     ->first();

$url = 'https://acp-api.amivoice.com/issue_service_authorization';
    
    $apiID = config('services.amivoice.api_id');
    $apiPW = config('services.amivoice.api_pw');
    // dd($apiPW);
    $data = [
     'sid' => $apiID,//変数名＝値
     'spw' => $apiPW,
     'epi' => 300000,
    ];
    $queryString = http_build_query($data);
 
$jsonData = json_encode($data);

// dd($jsonData);
$headers = [
    // 'Content-Type: application/json',
    'Authorization: Bearer ' . $jsonData
];

    
    $curl_handle = curl_init();//curlセッションを初期化して、curlハンドルを取得
    curl_setopt($curl_handle, CURLOPT_POST, TRUE);
    curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl_handle, CURLOPT_URL, $url);
    curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $queryString);
    curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $headers);

    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true); // curl_exec()の結果を文字列にする
    $json_response = curl_exec($curl_handle);
    if ($json_response === false) {
    echo 'Curl error: ' . curl_error($curl_handle);
} else {
}
    if(curl_exec($curl_handle) === false) {
    echo 'Curl error: ' . curl_error($curl_handle);
}
    
    curl_close($curl_handle);
  
  
    return view('notebookchange', compact('person', 'lastNotebook', 'json_response'));
}
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Speech  $speech
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $people_id, $id)
    {
      //データ更新
      $person = Person::find($request->people_id);
      $notebook = Notebook::findOrFail($id);
    
    // データ更新
    $notebook->notebook = $request->notebook;
    
    
    $notebook->save();
    
    return redirect()->route('people.index')->with('success', '更新されました。');
}
    
    
   

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Speech  $speech
     * @return \Illuminate\Http\Response
     */
    public function destroy(Speech $speech)
    {
        //
    }
}