<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class AttendanceRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('employee')->check() || auth('admin')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'breaks.*.start' => ['required', 'date_format:H:i'],
            'breaks.*.end' => ['required', 'date_format:H:i'],
            'reason' => ['required', 'string', 'max:255'],
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $start_time = $this->input('start_time');
            $end_time = $this->input('end_time');

            // 出勤時間と退勤時間の前後チェック
            if ($start_time >= $end_time) {
                $validator->errors()->add('start_time', '出勤時間もしくは退勤時間が不適切な値です');
                $validator->errors()->add('end_time', '出勤時間もしくは退勤時間が不適切な値です');
            }

            // 休憩時間の前後チェック
            foreach ($this->input('breaks', []) as $key => $break) {
                if (isset($break['start'], $break['end'])) {
                    if ($break['start'] >= $break['end']) {
                        $validator->errors()->add("breaks.$key.invalid_time", '休憩開始時間もしくは休憩終了時間が不適切な値です');
                    }

                    // 休憩時間が勤務時間外かチェック
                    if ($break['start'] < $start_time || $break['end'] > $end_time) {
                        $validator->errors()->add("breaks.$key.outside_working_hours", '休憩時間が勤務時間外です');
                    }
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'start_time.required' => '出勤時間を入力してください',
            'end_time.required' => '退勤時間を入力してください',
            'breaks.*.start.required' => '休憩開始時間を入力してください',
            'breaks.*.end.required' => '休憩終了時間を入力してください',
            'reason.required' => '備考を記入してください',
        ];
    }
}
