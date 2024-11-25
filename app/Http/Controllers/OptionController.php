<?php

namespace App\Http\Controllers;

use App\Models\Option;
use App\Models\OptionItem;
use App\Models\Person;
use App\Models\Facility;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class OptionController extends Controller
{
//     public function show($id)
// {
//     $person = Person::findOrFail($id);
//     $facility = $person->people_facilities()->first();
//     $options = Option::where('people_id', $id)->get();
//     $selectedItems = OptionItem::where('people_id', $id)->pluck('item1', 'item2', 'item3', 'item4', 'item5')->toArray();
    
//     $additionalItems = $options->map(function ($option) {
//         return [
//             'id' => $option->id,
//             'title' => $option->title,
//             'items' => $option->getItemsAsString(),
//         ];
//     })->toArray();

//     return view('people', compact('person', 'facility', 'options', 'selectedItems', 'additionalItems'));
// }

    public function store(Request $request, $people_id, $id)
{
    $request->validate([
        'title' => 'required',
        'facility_id' => 'required|exists:facilities,id',
        'item' => 'required|array|min:1',
        'item.*' => 'required|max:32',
    ], [
        'title.required' => 'タイトルは必須です',
        'facility_id.required' => '施設IDは必須です',
        'item.required' => '少なくとも1つの項目を入力してください',
        'item.*.required' => '記録項目は必須です',
        'item.*.max' => '各項目は32文字以内で入力してください',
    ]);

    $person = Person::findOrFail($people_id);

    $option = new Option();
    $option->title = $request->title;
    $option->people_id = $people_id;
    $option->facility_id = $request->facility_id;
    for ($i = 0; $i < 5; $i++) {
        $option->{"item" . ($i + 1)} = $request->item[$i] ?? null;
    }
    $option->flag = true;

    $option->save();

    // 二重送信防止
    $request->session()->regenerateToken();

    return redirect()->route('show.selected.items', ['people_id' => $people_id, 'id' => $id])
        ->with('message', '記録項目が追加されました。');
}

public function itemstore(Request $request)
{
    try {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'ユーザーが認証されていません。'
            ], 401);
        }

        $validated = $request->validate([
            'title' => 'required',
            'facility_id' => 'required|exists:facilities,id',
            'item' => 'required|array|min:1',
            'item.*' => 'required|max:32',
        ]);

        $facilities = $user->facility_staffs()->get();
        $facilityIds = $facilities->pluck('id')->toArray();
        if (!in_array($validated['facility_id'], $facilityIds)) {
            Log::warning("User {$user->id} attempted to access unauthorized facility {$validated['facility_id']}");
            return response()->json([
                'success' => false,
                'message' => '指定された施設にアクセスする権限がありません。'
            ], 403);
        }

        $people = Person::whereHas('people_facilities', function ($query) use ($validated) {
            $query->where('facilities.id', $validated['facility_id']);
        })->get();

        if ($people->isEmpty()) {
            Log::warning("No people found in facility {$validated['facility_id']}");
            return response()->json([
                'success' => false,
                'message' => '施設の利用者が見つかりません。'
            ], 404);
        }

        $optionGroupId = Str::uuid(); // 新しいUUIDを生成

        foreach ($people as $person) {
            $option = new Option();
            $option->title = $validated['title'];
            $option->people_id = $person->id;
            $option->facility_id = $validated['facility_id'];
            $option->option_group_id = $optionGroupId; // 新しいgroup_idを設定
            for ($i = 0; $i < 5; $i++) {
                $option->{"item" . ($i + 1)} = $validated['item'][$i] ?? null;
            }
            $option->flag = true;
            $option->save();
        }

        Log::info("Items stored successfully for all people in facility {$validated['facility_id']}");

        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => '全ての利用者に記録項目が追加されました。'
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error("Validation error: " . json_encode($e->errors()));
        return response()->json([
            'success' => false,
            'errors' => $e->errors(),
        ], 422);
    } catch (\Exception $e) {
        Log::error('Item store error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'サーバーエラーが発生しました。'
        ], 500);
    }
}
        
}

        



