<x-app-layout>

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
              font-size: 20px; /* フォントサイズを20ピクセルに設定 */
              text-decoration: underline;
            }
          </style>
      </div> 
      <div class="center-container">
            <div class="flex items-center justify-center my-2 font-bold text-2xl">
          <!--<div style="display: flex; align-items: center; margin-left: auto; margin-right: auto; max-width: 300px;">-->
              <h2>{{$person->last_name}}{{$person->first_name}}さんの様子・事業所に伝えたいこと</h2>
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

<form action="{{ route('hogoshatext_update', ['people_id' => $person->id, 'id' => $lastHogoshaText->id]) }}" method="POST" class="w-full max-w-lg mx-auto mt-4">
    @csrf
    <div class="flex flex-col items-center my-4">
   
        <textarea name="notebook" class="w-full max-w-lg font-bold" style="height: 300px;">{{ $lastHogoshaText ? $lastHogoshaText->notebook : '' }}</textarea>
    </div>
    <div class="flex justify-center my-2">
        <button type="submit" class="inline-flex items-center px-6 py-3 bg-gray-800 border border-transparent rounded-md font-semibold text-lg text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
            修正
        </button>
    </div>
</form>

<div id="messages" class="hidden"></div>
</body>
</html>

</x-app-layout>