<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\User;
use App\Models\Chat;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;//ログイン中のユーザIDを取得する
use App\Events\MessageSent;
use App\Models\Conversation;
use Intervention\Image\Facades\Image;//画像のアップロードに適用


class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
   {
        // データーベースの件数を取得
        $length = Chat::all()->count();

        // 表示する件数を代入
        $display = 5;

        $chats = Chat::offset($length-$display)->limit($display)->get();
       return view('chat', ['id' => $person->id],compact('person'));
   }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $person = Chat::findOrFail($request->people_id);

    return view('people', ['people' => Person::all()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

  
    
     public function store(Request $request, $people_id)
     {
         \Log::info('Store method called with people_id: ' . $people_id);
         \Log::info('Request data: ' . json_encode($request->all()));
     
         try {
             $person = Person::findOrFail($people_id);
     
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
     
             $directory = 'sample/chat_photo';
             $filename = null;
             $filepath = null;
     
             if ($request->hasFile('filename')) {
                 \Log::info('File received: ' . $request->file('filename')->getClientOriginalName());
                 $request->validate(['filename' => 'image|max:20480']);
                 $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'heic'];
                $originalExtension = strtolower($request->file('filename')->getClientOriginalExtension());

                if (!in_array($originalExtension, $allowedExtensions)) {
                    return response()->json(['error' => 'サポートされていないファイルタイプです'], 400);
                }
                 
                 $originalExtension = $request->file('filename')->getClientOriginalExtension();
     
                 if (strtolower($originalExtension) === 'heic') {
                     $filename = uniqid() . '.jpg';
                     $image = Image::make($request->file('filename'))->encode('jpg');
                     $path = $directory . '/' . $filename;
                     \Storage::disk('public')->put($path, (string) $image);
                     \Log::info('HEIC file converted and stored at: ' . $path);
                 } else {
                     $filename = uniqid() . '.' . $originalExtension;
                     $path = $request->file('filename')->storeAs($directory, $filename, 'public');
                     \Log::info('File stored at: ' . $path);
                 }
     
                 $filepath = 'storage/' . $directory . '/' . $filename;
             } else {
                 \Log::info('No file received in the request.');
             }
     
             $chat = Chat::create([
                 'people_id' => $people_id,
                 'user_name' => $user_name,
                 'user_identifier' => $user_identifier,
                 'message' => $request->message,
                 'filename' => $filename,
                 'path' => $filepath,
                 'last_name' => $user ? $user->last_name : null,
                 'first_name' => $user ? $user->first_name : null,
             ]);
     
             \Log::info('About to broadcast MessageSent event for chat ID: ' . $chat->id);
                 broadcast(new MessageSent($chat))->toOthers();
                 \Log::info('Broadcast method called for MessageSent event');
                     
                 return response()->json([
                     'id' => $chat->id,
                     'message' => $request->message,
                     'user_identifier' => $user_identifier,
                     'user_name' => $user_name,
                     'created_at' => $chat->created_at->format('Y-m-d H:i:s'),
                     'filename' => $chat->filename,
                     'last_name' => $chat->last_name,
                     'first_name' => $chat->first_name,
                 ]);
     
         } catch (\Exception $e) {
             \Log::error('Error in store method: ' . $e->getMessage());
             \Log::error('Stack trace: ' . $e->getTraceAsString());
             return response()->json(['error' => $e->getMessage()], 500);
         }
     }
    



    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Chat  $chat
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $people_id)
{

    $person = Person::findOrFail($people_id);
    $people = Person::all(); // ここで $people を取得
    // 現在ログインしているユーザーを取得
    $user = Auth::user();


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
    Chat::where('people_id', $people_id)
        ->where('user_identifier', '!=', $user_identifier)
        ->where('is_read', false)
        ->update(['is_read' => true]);

    // 指定されたpeople_idの未読メッセージの件数を取得
    // $unreadMessages = Chat::where('people_id', $people_id)->where('is_read', false)->count();


   // データベース内の指定されたpeople_idのチャットの件数を取得
    $length = Chat::where('people_id', $people_id)->count();


    // 指定されたpeople_idの最新の5件のチャットメッセージを取得
    $chats = Chat::where('people_id', $people_id)
                 ->orderBy('created_at', 'asc')
                 ->get(['*', 'filename', 'path']);

    $unreadMessages = Chat::where('people_id', $people_id)
    ->where('is_read', false)
    ->count();

    // ビューにデータを渡して表示
    return view('chat', ['id' => $person->id], compact('person', 'user_name', 'chats', 'unreadMessages'));
}


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Chat  $chat
     * @return \Illuminate\Http\Response
     */
    public function edit(Chat $chat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Chat  $chat
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Chat $chat)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Chat  $chat
     * @return \Illuminate\Http\Response
     */
    public function destroy(Chat $chat)
    {
        //
    }
}