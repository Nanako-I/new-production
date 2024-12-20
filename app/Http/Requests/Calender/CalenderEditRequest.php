<?php

namespace App\Http\Requests\Calender;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Enums\RoleType as RoleEnum;
use Illuminate\Support\Facades\Auth;

class CalenderEditRequest extends FormRequest
{
    public function authorize()
    {
        // 現在のユーザー情報を取得
        $user = Auth::user();

        return $user->hasRole([
            RoleEnum::SuperAdministrator,
            RoleEnum::FacilityStaffAdministrator,
            RoleEnum::FacilityStaffUser
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'scheduled_visit_id' => ['required', 'integer', 'exists:scheduled_visits,id'], // 追加
            'people_id' => ['required', 'integer'],
            'visit_type_id' => ['nullable', 'integer'],
            'arrival_datetime' => ['nullable', 'date_format:Y-m-d H:i:s'],
            'exit_datetime' => ['nullable', 'date_format:Y-m-d H:i:s'],
            'pick_up' => ['required', 'in:必要,不要'], 
            'drop_off' => ['required', 'in:必要,不要'],
            'notes' => ['nullable', 'text'],
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     */
    protected function failedValidation(Validator $validator)
    {
        $res = response()->json([
            'status' => 400,
            'errors' => $validator->errors(),
        ], 400);
        throw new HttpResponseException($res);
    }

    /**
     * リクエスト情報の取得
     *
     * @param  Request
     * @return array
     */
    public static function getOnlyRequest($request)
    {
        $array = $request->only([
            'scheduled_visit_id', // この行を追加
            'people_id',
            'visit_type_id',
            'arrival_datetime',
            'exit_datetime',
            'pick_up',
            'drop_off',
            'pick_up_time',
            'drop_off_time',
            'pick_up_staff',
            'drop_off_staff',
            'pick_up_bus',
            'drop_off_bus',
            'notes'
        ]);

        return $array;
    }
}
