<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facility;

class FacilityKeyController extends Controller
{
    public function show()
    {
        return view('auth.facility-key');
    }

    public function submit(Request $request)
    {
        $request->validate([
            'facility_key' => 'required|string|max:255',
        ]);

        // facilitiesテーブルでfacility_keyを確認
        $facility = Facility::where('facility_key', $request->facility_key)->first();

        if ($facility) {
            // facility_idをセッションに保存
            session(['facility_id' => $facility->id]);

            // 検証が成功したらログインページにリダイレクト
            return redirect()->route('login');
        } else {
            // 検証が失敗した場合、エラーメッセージを表示
            return redirect()->back()->withErrors(['facility_key' => '無効な施設キーです。']);
        }
    }
} 