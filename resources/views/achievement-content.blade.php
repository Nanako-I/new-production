<x-app-layout>

<!--ヘッダー[START]-->
<div class="flex items-center justify-center">
<div class="flex flex-col items-center">
    <form action="{{ url('people' ) }}" method="POST" class="w-full max-w-lg">
                        @method('PATCH')
                        @csrf
        <style>
            h2 {
              font-family: Arial, sans-serif; /* フォントをArialに設定 */
              font-size: 20px; /* フォントサイズを20ピクセルに設定 */
            }
        </style>
        <div class ="flex items-center justify-center"  style="padding: 20px 0;">
            <div class="flex flex-col items-center">
                <h2>できたこと</h2>
            </div>
        </div>
    </form>
  
         
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
        <script src="https://kit.fontawesome.com/de653d534a.js" crossorigin="anonymous"></script>
            <div class="flex flex-col items-start">
                <style>
                  p {
                    font-family: Arial, sans-serif; /* フォントをArialに設定 */
                    font-size: 25px; /* フォントサイズを20ピクセルに設定 */
                    font-weight: bold;
                  }
                </style>
                
                    <div class="flex items-center justify-center">
                         <input type="hidden" name="people_id" value="{{ $person->id }}">
                    </div>
                    <div style="display: flex; flex-direction: column; align-items: center;">
                      
                    
                    <div class="items-center justify-center p-4">
                        <input type="hidden" name="people_id" value="{{ $person->id }}">
                                                        
                                                        
                        <div class="flex flex-col items-start justify-center p-4">
                                    <div class="flex items-center mb-4">
                                        <input type="checkbox"  
                                            class="w-6 h-6 mr-2">
                                        <label for="" class="text-xl font-bold">歯みがき</label>
                                    </div>
                                    <div class="flex items-center mb-4">
                                        <input type="checkbox"  
                                            class="w-6 h-6 mr-2">
                                        <label for="" class="text-xl font-bold">着替え</label>
                                    </div>
                                    <div class="flex items-center mb-4">
                                        <input type="checkbox"  
                                            class="w-6 h-6 mr-2">
                                        <label for="" class="text-xl font-bold">時間を守れた</label>
                                    </div>
                                    <div class="flex items-center mb-4">
                                        <input type="checkbox"  
                                            class="w-6 h-6 mr-2">
                                        <label for="" class="text-xl font-bold">残さず食べられた</label>
                                    </div>
                                    <div class="flex items-center mb-4">
                                        <input type="checkbox"  
                                            class="w-6 h-6 mr-2">
                                        <label for="" class="text-xl font-bold">片付け</label>
                                    </div>
                                
                        </div>
                
             </div>
             <a href="{{ route('achievement.detail.show', ['people_id' => $person->id]) }}" class="relative">
                <button type="submit" class="bg-gray-800 text-white px-6 py-3 font-bold rounded mt-4">次へ</button>
             </a>
        </div>
    </div>
</x-app-layout>