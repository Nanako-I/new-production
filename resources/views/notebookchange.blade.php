<x-app-layout>
@if ($errors->any())
    <div style="color: red; font-weight: bold; background-color: #ffe6e6; border: 2px solid red; padding: 10px; border-radius: 5px;">
      <ul style="list-style-type: none; padding-left: 0;">
          @foreach ($errors->all() as $error)
              <li style="margin-bottom: 5px; color: red; font-weight: bold; font-size: 1.1em;">
                  <i class="fas fa-exclamation-triangle" style="margin-right: 5px;"></i>
                  {{ $error }}
              </li>
          @endforeach
      </ul>
    </div>
@endif

    <!--ヘッダー[START]-->
    
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="Content-Style-Type" content="text/css">
    <meta http-equiv="Content-Script-Type" content="text/javascript">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <!--<title>AmiVoice DSR WebSocket Recognition Protocol Checker</title>-->
    <script type="text/javascript" src="{{ asset('js/recorder.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/wrp.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/result.js') }}"></script>
     <form action="{{ url('people' ) }}" method="POST" class="w-full max-w-lg">
                        <!--@method('PATCH')-->
                        @csrf
  <body>
      <div style="display: flex; flex-direction: column;">
         <style>
         body {
              font-family: 'Noto Sans JP', sans-serif; /* フォントをArialに設定 */
              background: linear-gradient(135deg, rgb(253, 219, 146,0), rgb(209, 253, 255,1));
              }
            h2 {
              font-family: Arial, sans-serif; /* フォントをArialに設定 */
              font-size: 25px; /* フォントサイズを20ピクセルに設定 */
              /*text-decoration: underline;*/
            }
          </style>
      </div> 
      <div class="center-container">
            <div class="flex items-center text-align: center justify-center my-2 font-bold text-2xl">
          <!--<div style="display: flex; align-items: center; margin-left: auto; margin-right: auto; max-width: 300px;">-->
               <h2 style="margin: 0; padding: 0;">{{$person->last_name}}{{$person->first_name}}さんの連絡帳文章作成</h2>
            </div>
                @php
                $today = now()->toDateString();
                $todayLastNotebook = $person->notebooks()
                    ->whereDate('created_at', $today)
                    ->latest()
                    ->first();
                @endphp
                @if(!is_null($todayLastNotebook))
                <div class="flex items-center text-align: center justify-center my-2 font-bold text-2xl">
                 （{{$todayLastNotebook->created_at->format('n/jG：i')}}に登録した内容）
                </div>
                @endif
                
                
                <table border="0" width="100%" cellspacing="3" cellpadding="0">
                 <!--<tr><td width="270">&nbsp;サーバ URL</td>-->
                 <td><input type="text" class="hidden text" id="serverURL" spellcheck="false" tabindex="3"></td></tr>
                 
                  <!--<tr><td>&nbsp;接続エンジン名</td>-->
                  <td><input type="text" class="text hidden  grammarFileNames" spellcheck="false" tabindex="3"></td></tr>
                  <!--<tr><td>&nbsp;プロファイル ID</td>-->
                  <td><input type="text" class="text hidden" id="profileId" spellcheck="false" tabindex="3"></td></tr>
                   <tr class="options" style="display:none;"><td>&nbsp;プロファイル単語</td><td><input type="text" class="text" id="profileWords" spellcheck="false" tabindex="3"></td></tr>
                   <tr class="options" style="display:none;"><td>&nbsp;セグメンタプロパティ文字列</td><td><input type="text" class="text" id="segmenterProperties" spellcheck="false" tabindex="3"></td></tr>
                   <tr class="options" style="display:none;"><td>&nbsp;フィラー単語を保持するかどうか</td><td><input type="text" class="text" id="keepFillerToken" spellcheck="false" tabindex="3"></td></tr>
                   <tr class="options" style="display:none;"><td>&nbsp;認識中イベント発行間隔 (単位：ミリ秒)</td><td><input type="text" class="text" id="resultUpdatedInterval" spellcheck="false" tabindex="3"></td></tr>
                   <tr class="options" style="display:none;"><td>&nbsp;拡張情報</td><td><input type="text" class="text" id="extension" spellcheck="false" tabindex="3"></td></tr>
                   <!--<tr><td>&nbsp;APPKEY</td>-->
                   <td><input type="text" class="text hidden" id="authorization" spellcheck="false" tabindex="3"></td></tr>
                   <tr class="options" style="display:none;"><td>&nbsp;<font color="silver">音声データ形式</font></td><td><input type="text" class="text" id="codec" spellcheck="false" tabindex="3" readonly></td></tr>
                   <tr class="options" style="display:none;"><td>&nbsp;認識結果タイプ</td><td><input type="text" class="text" id="resultType" spellcheck="false" tabindex="3"></td></tr>
                   <tr class="options" style="display:none;"><td>&nbsp;無発話許容時間 (単位：ミリ秒)</td><td><input type="text" class="text" id="checkIntervalTime" spellcheck="false" tabindex="3"></td></tr>
                   <tr class="options" style="display:none;"><td>&nbsp;録音サンプリング周波数</td><td><input type="text" class="text" id="sampleRate" spellcheck="false" tabindex="3"></td></tr>
                   <tr class="options" style="display:none;"><td>&nbsp;連続録音可能時間 (単位: ミリ秒)</td><td><input type="text" class="text" id="maxRecordingTime" spellcheck="false" tabindex="3"></td></tr>
                   <tr class="options" style="display:none;"><td>&nbsp;ダウンサンプリング</td><td><input type="checkbox" class="checkbox" id="downSampling" tabindex="3"></td></tr>
                   <tr class="options" style="display:none;"><td>&nbsp;ADPCM 圧縮</td><td><input type="checkbox" class="checkbox" id="adpcmPacking" tabindex="3"></td></tr>
                   <tr>
                   <!--<tr><td width="270">&nbsp;ワンタイムAppKey発行API URL</td>-->
                   <td><input type="text" class="text hidden" id="issuerURL" spellcheck="false" tabindex="1"></td></tr>
                   <!--<tr><td>&nbsp;サービス ID</td>-->
                   <td><input type="text" class="text hidden" id="sid" spellcheck="false" tabindex="1"></td></tr>
                   <!--<tr><td>&nbsp;サービスパスワード</td>-->
                   <td><input type="password" class="text hidden" id="spw" spellcheck="false" autocomplete="on" tabindex="1"></td></tr>
                   <tr class="issue_options" style="display:none;"><td>&nbsp;有効期限</td><td><input type="text" class="text" id="epi" spellcheck="false" tabindex="1"></td></tr>
                   <tr>
                    <td>
                  <input type="button" value="サービス認証キーの取得" class="text hidden" id="issueButton" tabindex="2">
                  </td>
                  <td>
                   &nbsp;
                  </td>
                 </tr>
                </table>
              
               <!--amivoiceのAPIキー認証部分↓-->
               <!--<input type="text" class="text" id="authorization" spellcheck="false">-->
            <div class="flex items-center justify-center m-2">
             <p class="font-bold text-xl">音声で入力する場合、スタートボタンを押してください</p>
            </div>
        </form>
        <style>
          .center-container {
          display: flex;
          flex-direction: column;
          justify-content: center;
          /*align-items: center;*/
          height: 100vh;
          width:100vw;;
          }
          </style>
          <div class="flex items-center justify-center">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
              <script src="https://kit.fontawesome.com/de653d534a.js" crossorigin="anonymous"></script>
                <i class="fa-solid fa-volume-high text-orange-400" style="font-size: 3em; padding: 0 5px;"></i>
         
                <button id="resumePauseButton" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded-lg text-lg mx-1">
                  音声で入力
                </button>
                <div id="result-div"></div>
          </div>
    <form action="{{ route('notebook_update', ['people_id' => $person->id, 'id' => $lastNotebook->id]) }}" method="POST" class="w-full max-w-lg mx-auto mt-4">
          
      @csrf
        <div style="display: flex; flex-direction: column; align-items: center; margin: 10px 0;">
            <div class="flex flex-col items-center w-full my-4">
                <textarea 
                    id="recognitionResult" 
                    name="notebook" 
                    class="w-full p-4 border rounded-lg shadow-sm font-bold"
                    style="min-height: 300px; resize: vertical;"
                >{{ $lastNotebook ? $lastNotebook->notebook : '' }}</textarea>
            </div>
            <span class="recognitionResultText"></span><span class="recognitionResultInfo"></span>
        </div>
        <div style="display: flex; align-items: center; justify-content: center;" class="my-2">
            <button type="submit" class="inline-flex items-center px-6 py-3 bg-gray-800 border border-transparent rounded-md font-semibold text-lg text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                修正
            </button>
        </div>
      </form>
         <p id="amivoiceApiKeyContainer" data-api-key="{{ $json_response }}"></p>

      <form action="{{ route('notebook.delete', ['people_id' => $person->id, 'id' => $lastNotebook->id]) }}" method="POST">
        @csrf
          <div class="flex justify-center my-4">
              <button type="button" class="delete-btn font-semibold px-4 py-2 bg-gray-600 text-lg text-white rounded-md hover:bg-gray-500" data-id="{{ $lastNotebook->id  }}" data-toggle="modal" data-target="#confirmDeleteModal">
              このデータを削除する
              </button>
          </div>
      </form>
</div>




<!-- モーダルダイアログ -->
<div class="modal fixed w-full h-full top-0 left-0 flex items-center justify-center hidden" id="confirmDeleteModal">
    <div class="modal-overlay absolute w-full h-full bg-gray-600 opacity-50"></div>

    <div class="modal-container bg-white w-full max-w-xs mx-auto rounded shadow-lg z-50 overflow-y-auto">
        <div class="modal-content py-4 text-left px-6">
            <!--Title-->
            <div class="flex justify-between items-center pb-3">
                <p class="text-2xl font-semibold">本当に削除しますか？</p>
                <div class="modal-close cursor-pointer z-50" data-dismiss="modal">
                <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
                    <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                </svg>
                </div>
            </div>

            <!--Body-->
            <p class="text-base font-normal">削除したデータは復元できません。</p>
            <!--Footer-->
            <div class="flex justify-end pt-2">
                <button type="button" class="px-4 bg-blue-800 p-3 rounded-lg text-white hover:bg-blue-400 mr-2" data-dismiss="modal">キャンセル</button>
                <button type="button" class="px-4 bg-red-500 p-3 rounded-lg text-white hover:bg-red-400" id="deleteBtn">削除</button>
            </div>
        </div>
    </div>
</div>

<div id="messages" class="hidden"></div>


<script type="text/javascript">

document.addEventListener('DOMContentLoaded', function () {
    var deleteForm; // 削除するフォームを保存する変数

    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            deleteForm = this.closest('form'); // フォームを取得して保存
            document.getElementById('confirmDeleteModal').classList.remove('hidden');
        });
    });

    document.getElementById('deleteBtn').addEventListener('click', function() {
        deleteForm.submit(); // モーダルの削除ボタンがクリックされたらフォームを送信
        document.getElementById('confirmDeleteModal').classList.add('hidden');
    });

    document.querySelectorAll('[data-dismiss="modal"]').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('confirmDeleteModal').classList.add('hidden');
        });
    });
});

setTimeout(() => {
	

(function() {
  // <!--
  function log_(n, s) {
   // ログメッセージをコンソールに表示
    console.log(n + s);
    // ログメッセージの種類に応じて色を設定
    var color = "";
    if (s.lastIndexOf("EVENT: ", 0) != -1) {
//    color = "green";
    } else
    if (s.lastIndexOf("INFO: ", 0) != -1) {
//    color = "blue";
    } else
    if (s.lastIndexOf("ERROR: ", 0) != -1) {
      color = "red";
    } else {
      color = "black";
    }
    if (color) {
    // 表示するメッセージ数が20以上の場合、一番古いメッセージを削除
      if (messages.childNodes.length >= 20) {
        messages.removeChild(messages.lastChild);
      }
      // 新しいdiv要素（ブラウザの下部にコンソールログみたいな内容を表示させる部分）を生成し、スタイルを設定して挿入
      messages.insertBefore(document.createElement("div"), messages.firstChild).innerHTML = n + s;
      messages.firstChild.style.borderBottom = "1px #ddd solid";
      messages.firstChild.style.color = color;
    }
  }
  function sanitize_(s) {
    return s.replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/&apos;/g, '&apos;')
            .replace(/&quot;/g, '&quot;');
  }
  // -->
  // 音声認識サーバへの接続処理が開始した時に呼び出されます。
  function connectStarted() {
  
  //EVENT: connectStarted()"という文字を表示させる
    log_(this.name, "EVENT: connectStarted()");
  }

  // 音声認識サーバへの接続処理が完了した時に呼び出されます。
  function connectEnded() {
    log_(this.name, "EVENT: connectEnded()");
  }

  // 音声認識サーバからの切断処理が開始した時に呼び出されます。
  function disconnectStarted() {
    log_(this.name, "EVENT: disconnectStarted()");
  }

  // 音声認識サーバからの切断処理が完了した時に呼び出されます。
  function disconnectEnded() {
    log_(this.name, "EVENT: disconnectEnded()");
    // 「録音の開始」ボタンの制御
    resumePauseButton.innerHTML = "音声で入力";
    //ボタンが再度操作可能になるように（ボタンを有効化）する↓
    resumePauseButton.disabled = false;
    resumePauseButton.classList.remove("sending");
  }

  // 音声認識サーバへの音声データの供給開始処理が開始した時に呼び出されます。
  function feedDataResumeStarted() {
    log_(this.name, "EVENT: feedDataResumeStarted()");
  }

  // 音声認識サーバへの音声データの供給開始処理が完了した時に呼び出されます。
  function feedDataResumeEnded() {
    log_(this.name, "EVENT: feedDataResumeEnded()");
    // ボタンの制御
    resumePauseButton.innerHTML = "<br><br>音声データの録音中...<br><br><span class=\"supplement\" >音声入力を停止する</span>";
    resumePauseButton.disabled = false;
    resumePauseButton.classList.add("sending");
  }

  // 音声認識サーバへの音声データの供給終了処理が開始した時に呼び出されます。
  function feedDataPauseStarted() {
    log_(this.name, "EVENT: feedDataPauseStarted()");
  }

  // 音声認識サーバへの音声データの供給終了処理が完了した時に呼び出されます。
  function feedDataPauseEnded(reason) {
    log_(this.name, "EVENT: feedDataPauseEnded(): reason[code[" + reason.code + "] message[" + reason.message + "]]");
  }

  // 発話区間の始端が検出された時に呼び出されます。
  function utteranceStarted(startTime) {
    log_(this.name, "EVENT: utteranceStarted(): endTime[" + startTime + "]");
  }

  // 発話区間の終端が検出された時に呼び出されます。
  function utteranceEnded(endTime) {
    log_(this.name, "EVENT: utteranceEnded(): endTime[" + endTime + "]");
  }

  // 認識処理が開始された時に呼び出されます。
  function resultCreated() {
    log_(this.name, "EVENT: resultCreated()");
    this.recognitionResultText.innerHTML = "...";
    this.recognitionResultInfo.innerHTML = "";
    this.startTime = new Date().getTime();
  }

  // 認識処理中に呼び出されます。
  function resultUpdated(result) {
    log_(this.name, "EVENT: resultUpdated(): result[" + result + "]");
    result = Result.parse(result);
    var text = (result.text) ? sanitize_(result.text) : "...";
    this.recognitionResultText.innerHTML = text;
  }

  // 認識処理が確定した時に呼び出されます。
  function resultFinalized(result) {
    log_(this.name, "EVENT: resultFinalized(): result[" + result + "]");
    // Result クラスを使用して結果をパース
    result = Result.parse(result);
    if (result.text) {
    // 結果のテキストをサニタイズ
    var text = sanitize_(result.text);
    // 結果の情報を取得
    var duration = result.duration;
    var elapsedTime = new Date().getTime() - this.startTime;
    var confidence = result.confidence;
    // RT (Real Time) および CF (Confidence) の計算
    var rt = ((duration > 0) ? (elapsedTime / duration).toFixed(2) : "-") + " (" + (elapsedTime / 1000).toFixed(2) + "/" + ((duration > 0) ? (duration / 1000).toFixed(2) : "-") + ")";
    var cf = (confidence >= 0.0) ? confidence.toFixed(2) : "-";
    
    // 認識結果を表示する要素に結果をセット
    //this.recognitionResultText.innerHTML = text;
    //this.recognitionResultInfo.innerHTML = "RT: " + rt + "<br>CF: " + cf;
    
    var textareaElement = document.getElementById("recognitionResult");
    //textareaElement.value += text + "\n"; // 新しいテキストを追加し、改行して区切る
    textareaElement.value += text;
    //this.recognitionResultInfo.innerHTML = "RT: " + rt + "<br>CF: " + cf;
    
     // ログに認識結果の詳細情報を記録
    log_(this.name, text + " <font color=\"darkgray\">(RT: " + rt + ") (CF: " + cf + ")</font>");
  }
  }

  // 各種イベントが通知された時に呼び出されます。
  function eventNotified(eventId, eventMessage) {
    log_(this.name, "EVENT: eventNotified(): eventId[" + eventId + "] eventMessage[" + eventMessage + "]");
  }

  // メッセージの出力が要求された時に呼び出されます。
  function TRACE(message) {
    log_(this.name || "", message);
  }

  // 画面要素の取得
  var issuerURL = document.getElementById("issuerURL");
  var sid = document.getElementById("sid");
  var spw = document.getElementById("spw");
  var epi = document.getElementById("epi");
  var issueButton = document.getElementById("issueButton");
  var grammarFileNames = document.getElementsByClassName("grammarFileNames");
  var recognitionResultText = document.getElementsByClassName("recognitionResultText");
  var recognitionResultInfo = document.getElementsByClassName("recognitionResultInfo");

  // 音声認識ライブラリのプロパティ要素の設定
  Wrp.serverURLElement = serverURL;
  Wrp.grammarFileNamesElement = grammarFileNames[0];
  Wrp.profileIdElement = profileId;
  Wrp.profileWordsElement = profileWords;
  Wrp.segmenterPropertiesElement = segmenterProperties;
  Wrp.keepFillerTokenElement = keepFillerToken;
  Wrp.resultUpdatedIntervalElement = resultUpdatedInterval;
  Wrp.extensionElement = extension;
  Wrp.authorizationElement = authorization;
  //Wrp.authorizationElement = amivoiceApiKey;　//←変更してみた
  Wrp.codecElement = codec;
  Wrp.resultTypeElement = resultType;
  Wrp.checkIntervalTimeElement = checkIntervalTime;
  Wrp.issuerURLElement = issuerURL;
  Wrp.sidElement = sid;
  Wrp.spwElement = spw;
  Wrp.epiElement = epi;
  Wrp.name = "";
  Wrp.recognitionResultText = recognitionResultText[0];
  Wrp.recognitionResultInfo = recognitionResultInfo[0];

  // 音声認識ライブラリのイベントハンドラの設定
  Wrp.connectStarted = connectStarted;
  Wrp.connectEnded = connectEnded;
  Wrp.disconnectStarted = disconnectStarted;
  Wrp.disconnectEnded = disconnectEnded;
  Wrp.feedDataResumeStarted = feedDataResumeStarted;
  Wrp.feedDataResumeEnded = feedDataResumeEnded;
  Wrp.feedDataPauseStarted = feedDataPauseStarted;
  Wrp.feedDataPauseEnded = feedDataPauseEnded;
  Wrp.utteranceStarted = utteranceStarted;
  Wrp.utteranceEnded = utteranceEnded;
  Wrp.resultCreated = resultCreated;
  Wrp.resultUpdated = resultUpdated;
  Wrp.resultFinalized = resultFinalized;
  Wrp.eventNotified = eventNotified;
  Wrp.TRACE = TRACE;

  // 録音ライブラリのプロパティ要素の設定
  Recorder.sampleRateElement = sampleRate;
  Recorder.maxRecordingTimeElement = maxRecordingTime;
  Recorder.downSamplingElement = downSampling;
  Recorder.adpcmPackingElement = adpcmPacking;

  // 画面要素の初期化
  issuerURL.value = "https://acp-api.amivoice.com/issue_service_authorization";
  serverURL.value = "wss://acp-api.amivoice.com/v1/";
  grammarFileNames[0].value = Wrp.grammarFileNames;
  profileId.value = Wrp.profileId;
  profileWords.value = Wrp.profileWords;
  segmenterProperties.value = Wrp.segmenterProperties;
  keepFillerToken.value = Wrp.keepFillerToken;
  resultUpdatedInterval.value = Wrp.resultUpdatedInterval;
  extension.value = Wrp.extension;
  
  authorization.value = Wrp.authorization;
  //document.getElementById('authorization').value = amivoiceApiKey;//←変更してみた
  codec.value = Wrp.codec;
  resultType.value = Wrp.resultType;
  checkIntervalTime.value = Wrp.checkIntervalTime;
  sampleRate.value = Recorder.sampleRate;
  maxRecordingTime.value = Recorder.maxRecordingTime;
  downSampling.checked = Recorder.downSampling;
  adpcmPacking.checked = Recorder.adpcmPacking;

  // 音声認識ライブラリ／録音ライブラリのメソッドの画面要素への登録
  resumePauseButton.onclick = function() {
    // 音声認識サーバへの音声データの供給中かどうかのチェック
    if (Wrp.isActive()) {
      // 音声認識サーバへの音声データの供給中の場合...
      // 音声認識サーバへの音声データの供給の停止
      Wrp.feedDataPause();

      // ボタンの制御
      resumePauseButton.disabled = true;
    } else {
      // 音声認識サーバへの音声データの供給中でない場合...
      // グラマファイル名が指定されているかどうかのチェック
      if (Wrp.grammarFileNamesElement.value != "") {
        // グラマファイル名が指定されている場合...
        // 音声認識サーバへの音声データの供給の開始
        Wrp.feedDataResume();

        // ボタンの制御
        resumePauseButton.disabled = true;
      } else {
        // グラマファイル名が指定されていない場合...
        // (何もしない)
      }
    }
  };
  
  //「サービス認証キーを取得する」ボタンをクリックした時の処理
  issueButton.onclick = Wrp.issue;

  var issue_options = document.querySelectorAll(".issue_options");
  function toggle_issue_options() {
    issue_options[0].style.display = (issue_options[0].style.display === "") ? "none" : "";
    for (var i = 1; i < issue_options.length; i++) {
      issue_options[i].style.display = issue_options[0].style.display;
    }
  }
  var toggle_issue_optionss = document.querySelectorAll(".toggle_issue_options");
  for (var i = 0; i < toggle_issue_optionss.length; i++) {
    toggle_issue_optionss[i].onclick = toggle_issue_options;
    toggle_issue_optionss[i].style.cursor = "pointer";
  }

  var options = document.querySelectorAll(".options");
  function toggle_options() {
    options[0].style.display = (options[0].style.display === "") ? "none" : "";
    for (var i = 1; i < options.length; i++) {
      options[i].style.display = options[0].style.display;
    }
  }
  var toggle_optionss = document.querySelectorAll(".toggle_options");
  for (var i = 0; i < toggle_optionss.length; i++) {
    toggle_optionss[i].onclick = toggle_options;
    toggle_optionss[i].style.cursor = "pointer";
  }

  version.innerHTML = Wrp.version + " " + Result.version;
})();
}, "1000");
</script>
</body>
</html>

</x-app-layout>