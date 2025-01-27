<?php

namespace App\Http\Controllers;

use App\Http\Requests\Calender\CalenderDeleteRequest;
use App\Http\Requests\Calender\CalenderEditRequest;
use App\Http\Requests\Calender\CalenderIndexPersonRequest;
use App\Http\Requests\Calender\CalenderIndexScheduledVisitRequest;
use App\Http\Requests\Calender\CalenderRegisterRequest;
use App\Http\Requests\Calender\CalenderScheduledVisitDetailRequest;
use App\Http\Traits\MessageTrait;
use App\Models\Person;
use App\Models\ScheduledVisit;
use App\Models\VisitType;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CalenderController extends Controller
{
    use MessageTrait;

    /**
     * 利用者一覧を取得
     *
     * @param CalenderIndexPersonRequest $request
     * @return JsonResponse
     */
    public function indexPerson(CalenderIndexPersonRequest $request)
    {
        // $id = $request->input('id'); // リクエストからIDを取得
        // $scheduledVisit = ScheduledVisit::findOrFail($id);
        $form_request = new CalenderIndexPersonRequest();
        $form_request->authorize($request);
        try {
            $user = Auth::user();
            $facility = $user->facility_staffs()->first();
            if ($facility) {
                $people = $facility->people_facilities()->get();
                $response = $people->isNotEmpty() ? self::returnMessageIndex($people) : self::returnMessageNodataArray();
                $status = $people->isNotEmpty() ? Response::HTTP_OK : Response::HTTP_NO_CONTENT;
            } else {
                $response = self::returnMessageNodataArray();
                $status = Response::HTTP_NO_CONTENT;
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $response = self::messageErrorStatusText($message);
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        }
        return response()->json($response, $status);
    }

    /**
     * 訪問タイプ一覧を取得
     *
     * @return JsonResponse
     */
    public function indexVisitType()
    {
        try {
            $data = VisitType::all();
            if ($data->isEmpty()) {
                $response = self::returnMessageNodataArray();
                $status = Response::HTTP_NO_CONTENT;
            }
            $response = self::returnMessageIndex($data);
            $status = Response::HTTP_OK;
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $response = self::messageErrorStatusText($message);
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        }
        return response()->json($response, $status);
    }

    /**
     * 訪問スケジュール一覧を取得
     *
     * @param CalenderIndexScheduledVisitRequest $request
     * @return JsonResponse
     */
    public function indexScheduledVisit(CalenderIndexScheduledVisitRequest $request)
    {
        $form_request = new CalenderIndexScheduledVisitRequest();
        $form_request->authorize($request);
        try {
            $user = Auth::user();
            $facility = $user->facility_staffs()->first();
            if ($facility) {
                $people = $facility->people_facilities()->get();
                if ($people) {
                    $peopleIds = $people->pluck('id');
                    $scheduled_visits = ScheduledVisit::whereIn('people_id', $peopleIds)->get();
                    $scheduled_visits->each(function ($schedule) {
                        $schedule->type = VisitType::find($schedule->visit_type_id)->type;
                        $person = Person::find($schedule->people_id);
                        $schedule->person_name = $person->last_name . ' ' . $person->first_name;

                        // $schedule->person_name = Person::find($schedule->people_id)->person_name;
                    });
                    $response = self::returnMessageIndex($scheduled_visits);
                    $status = Response::HTTP_OK;
                } else {
                    $response = self::returnMessageNodataArray();
                    $status = Response::HTTP_NO_CONTENT;
                }
            } else {
                $response = self::returnMessageNodataArray();
                $status = Response::HTTP_NO_CONTENT;
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $response = self::messageErrorStatusText($message);
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        }
        return response()->json($response, $status);
    }


    /**
     * 特定の訪問予定を取得
     *
     * @param CalenderScheduledVisitDetailRequest $request
     * @return JsonResponse
     */
    public function getScheduledVisitDetail(CalenderScheduledVisitDetailRequest $request)
    {
        $array = CalenderScheduledVisitDetailRequest::getOnlyRequest($request);

        try {
            $schedule = ScheduledVisit::find($array['scheduled_visit_id']);
            if (!$schedule) {
                $response = self::returnMessageNodataArray();
                $status = Response::HTTP_NO_CONTENT;
            }
            $response = self::returnMessageIndex($schedule);
            $status = Response::HTTP_OK;
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $response = self::messageErrorStatusText($message);
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        }
        return response()->json($response, $status);
    }


    public function rules()
    {
        return [
            'people_id' => 'required|integer',
            'visit_type_id' => 'required|integer',
            'arrival_datetime' => 'required|date',
            'exit_datetime' => 'nullable|date',
            'pick_up' => 'nullable|string|in:必要,不要',
            'drop_off' => 'nullable|string|in:必要,不要',
            'pick_up_time' => 'nullable|date',
            'drop_off_time' => 'nullable|date',
            'pick_up_staff' => 'nullable|string|max:255',
            'drop_off_staff' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ];
    }
    /**
     * カレンダーに利用者の訪問予定を登録する
     *
     * @param CalenderRegisterRequest $request
     * @return JsonResponse
     */
    public function register(CalenderRegisterRequest $request)
    {
        try {
            // リクエストデータのログ
            \Log::info('Request data:', $request->all());

            $array = CalenderRegisterRequest::getOnlyRequest($request);

            // バリデーション前のデータチェック
            \Log::info('Validated data:', $array);

            DB::beginTransaction();

            $result = ScheduledVisit::create([
                'people_id' => $array['people_id'],
                'arrival_datetime' => $array['arrival_datetime'],
                'exit_datetime' => $array['exit_datetime'] ?? $array['arrival_datetime'], // デフォルト値を設定
                'visit_type_id' => $array['visit_type_id'],
                'notes' => $array['notes'],
                'pick_up' => $array['pick_up'],
                'drop_off' => $array['drop_off'],
                'pick_up_time' => $array['pick_up_time'],
                'drop_off_time' => $array['drop_off_time'],
                'pick_up_staff' => $array['pick_up_staff'],
                'drop_off_staff' => $array['drop_off_staff'],
                'pick_up_bus' => $array['pick_up_bus'],
                'drop_off_bus' => $array['drop_off_bus'],
            ]);

            \Log::info('Created record:', ['result' => $result]);

            DB::commit();
            return response()->json(['message' => '登録成功'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Registration error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => '登録に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * カレンダーに利用者の訪問予定を編集する
     *
     * @param CalenderEditRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function edit(CalenderEditRequest $request, $id)
    {
        \Log::info('編集リクエストデータ:', $request->all());
        $array = array_merge(
            CalenderEditRequest::getOnlyRequest($request),
            ['scheduled_visit_id' => $id]
        );

        DB::beginTransaction();
        try {
            $scheduledVisit = ScheduledVisit::findOrFail($id);
            $scheduledVisit->update($array);

            DB::commit();
            return response()->json(['message' => '更新成功'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('更新エラー:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => '更新に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * カレンダーの訪問予定を削除する
     *
     * @param CalenderDeleteRequest $request
     * @return JsonResponse
     */
    public function delete(CalenderDeleteRequest $request)
    {
        $array = CalenderDeleteRequest::getOnlyRequest($request);

        DB::beginTransaction();
        try {
            $schedule = ScheduledVisit::find($array['schedule_id']);
            if ($schedule) {
                $schedule->delete();
                $response = self::returnMessageIndex(true);
                $status = Response::HTTP_OK;
            } else {
                throw new \Exception('No schedule found.');
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $message = $e->getMessage();
            $response = self::messageErrorStatusText($message);
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        }
        return response()->json($response, $status);
    }
};
