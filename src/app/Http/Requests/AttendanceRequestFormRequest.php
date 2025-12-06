<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class AttendanceRequestFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'break_start' => 'array',
            'break_start.*' => 'nullable|date_format:H:i',
            'break_end' => 'array',
            'break_end.*' => 'nullable|date_format:H:i',
            'reason' => 'required|string|max:100',
        ];
    }


    public function messages(): array
    {
        return [
            'reason.required' => '備考を記入してください',
            'reason.max' => '修正理由は100文字以内で入力してください。',
            'start_time.date_format' => '出勤時刻の形式が正しくありません。',
            'end_time.date_format' => '退勤時刻の形式が正しくありません。',
            'break_start.*.date_format' => '休憩開始時刻の形式が正しくありません。',
            'break_end.*.date_format' => '休憩終了時刻の形式が正しくありません。',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $startTime = $this->input('start_time');
            $endTime = $this->input('end_time');
            $breakStarts = $this->input('break_start', []);
            $breakEnds = $this->input('break_end', []);

            // ルール1: 出勤時刻が退勤時刻より後になっている場合
            if ($startTime && $endTime) {
                $start = Carbon::createFromFormat('H:i', $startTime);
                $end = Carbon::createFromFormat('H:i', $endTime);

                if ($start->greaterThanOrEqualTo($end)) {
                    $validator->errors()->add('end_time', '出勤時間もしくは退勤時間が不適切な値です');
                }
            }

            // ルール2: 休憩時間が退勤時間より後になっている場合
            //休憩開始時刻は退勤時刻よりも後ならエラー
            if ($endTime && $startTime) {
                $startTimeCarbon = Carbon::createFromFormat('H:i', $startTime);
                $endTimeCarbon = Carbon::createFromFormat('H:i', $endTime);

                foreach ($breakStarts as $index => $breakStart) {
                    if (!empty($breakStart)) {
                        $breakStartTime = Carbon::createFromFormat('H:i', $breakStart);

                        if (
                            $breakStartTime->lessThan($startTimeCarbon) ||
                            $breakStartTime->greaterThan($endTimeCarbon)
                        ) {
                            $validator->errors()->add("break_start.$index", "休憩時間は不適切な値です");
                        }
                    }
                }
            }


            //休憩終了時間が退勤時間よりも後だったらエラー
            foreach ($breakEnds as $index => $breakEnd) {
                if (!empty($breakEnd) && !empty($endTime)) {
                    $breakEndTime = Carbon::createFromFormat('H:i', $breakEnd);
                    $endTimeCarbon = Carbon::createFromFormat('H:i', $endTime);

                    if ($breakEndTime->greaterThanOrEqualTo($endTimeCarbon)) {
                        $validator->errors()->add("break_end.$index", "休憩時間もしくは退勤時間が不適切な値です");
                    }
                }
            }
        });
    }
}
