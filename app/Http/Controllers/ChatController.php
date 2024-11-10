<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\User;
use App\Models\Chat;
// 追記↓
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;//ログイン中のユーザIDを取得する
use App\Events\MessageSent;
use App\Models\Conversation;


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

    //  public function show(Request $request, $conversationId)
    // {
    //     $conversation = Conversation::findOrFail($conversationId);
    //     $chats = $conversation->chats()->orderBy('created_at', 'asc')->get();

    //     return view('chats', compact('conversation', 'chats'));
    // }

     // 新しいメッセージを保存する
    //  public function store(Request $request, $conversationId)
    //  {
    //      $request->validate([
    //          'message' => 'required|string',
    //      ]);

    //      $chat = new Chat();
    //      $chat->conversation_id = $conversationId;
    //      $chat->user_id = auth()->id(); // ログインユーザーのID
    //      $chat->user_name = auth()->user()->name;
    //      $chat->user_identifier = auth()->user()->email; // 例としてメールアドレスを使用
    //      $chat->message = $request->input('message');
    //      $chat->save();

    //      return redirect()->route('chat.show', $conversationId);
    //  }

    public function store(Request $request, $people_id)
    {
        try {
            $user = Auth::user();
            $user_identifier = $user->id;
            
            // ファイルがアップロードされた場合の処理
            if ($request->hasFile('filename')) {
                $file = $request->file('filename');
                
                // バリデーション
                $request->validate([
                    'filename' => 'image|max:2048',
                ]);

                // ディレクトリの存在確認と作成
                $directory = 'public/sample/chat_photo';
                if (!\Storage::exists($directory)) {
                    \Storage::makeDirectory($directory);
                }

                // ファイル名の生成と保存
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs($directory, $filename);

                // パーミッションの設定
                $fullPath = storage_path('app/' . $directory . '/' . $filename);
                chmod($fullPath, 0644);
                chown($fullPath, 'apache');
                chgrp($fullPath, 'apache');
            } else {
                $filename = null;
            }

            $chat = Chat::create([
                'message' => $request->message,
                'user_identifier' => $user_identifier,
                'people_id' => $people_id,
                'last_name' => $user->last_name,
                'first_name' => $user->first_name,
                'filename' => $filename,
                'is_read' => false
            ]);

            return response()->json([
                'message' => $request->message,
                'user_identifier' => $user_identifier,
                'last_name' => $user->last_name,
                'first_name' => $user->first_name,
                'created_at' => $chat->created_at->format('Y-m-d H:i:s'),
                'filename' => $filename
            ]);

        } catch (\Exception $e) {
            \Log::error('Chat store error: ' . $e->getMessage());
            return response()->json(['error' => 'メッセージの保存に失敗しました。'], 500);
        }
    }




    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Chat  $chat
     * @return \Illuminate\Http\Response
     */
    public function show($people_id)
    {
        $person = Person::findOrFail($people_id);
        $user = Auth::user();
        $user_identifier = $user->id;
        $user_name = $user->last_name . ' ' . $user->first_name;

        // チャット画面を表示する際に、未読メッセージを既読にする
        Chat::where('people_id', $people_id)
            ->where('user_identifier', '!=', $user_identifier)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $length = Chat::where('people_id', $people_id)->count();
        $display = 5;

        $chats = Chat::where('people_id', $people_id)
                     ->offset($length - $display)
                     ->limit($display)
                     ->get();

        $unreadMessages = Chat::where('people_id', $people_id)
            ->where('is_read', false)
            ->count();

        return view('chat', ['id' => $person->id], compact('person', 'user', 'chats', 'unreadMessages', 'user_name'));
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