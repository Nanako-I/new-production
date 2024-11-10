<?php

namespace App\Http\Controllers;

use App\Models\Notebook;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
// use Google\Cloud\Speech\V1\SpeechClient;
// use Google\Cloud\Speech\V1\RecognitionConfig;
// use Google\Cloud\Speech\V1\RecognitionConfig\AudioEncoding;
// use Google\Cloud\Speech\V1\StreamingRecognitionConfig;
// use Google\Cloud\Speech\V1\StreamingRecognizeRequest;

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
    return view('people', compact('notebook', 'people'));
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
    $notebooks = $person->notebooks;
    


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
    return view('notebookwriting', ['id' => $person->id],compact('person', 'json_response'));
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


public function change(Request $request, $people_id)
{
    $person = Person::findOrFail($people_id);
    
    // その日の最後のnotebookを取得
    $lastNotebook = $person->notebooks()
        ->whereDate('created_at', now()->toDateString())
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
  
  
    return view('notebookchange', compact('person', 'lastNotebook', 'json_response'));
}
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Speech  $speech
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Notebook $notebook)
    {
      //データ更新
        $person = Person::find($request->people_id);
        $notebook->people_id = $person->id;
        $notebook->notebook = $request->notebook;
        
        $notebook->save();
        
        $people = Person::all();
        
        return view('people', compact('notebook', 'people'));
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