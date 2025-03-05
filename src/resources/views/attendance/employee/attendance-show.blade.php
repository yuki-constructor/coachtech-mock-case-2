@extends('layouts.employee-app')

@section('title', '勤怠詳細画面（従業員）')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/attendance/employee/attendance-show.css') }}">
@endpush

@section('content')
    <div class="container-wrap">
        <div class="container">
            <h1>勤怠詳細</h1>
            <form action="{{ route('employee.attendance.request', ['attendanceId' => $attendance->id]) }}" method="POST">
                @csrf
                <div class="attendance-table">

                    {{-- 名前 --}}
                    <div class="table-row">
                        <span class="label">名前</span>
                        <span class="value">{{ auth('employee')->user()->name }}</span>
                        <span class="error-message"></span>
                    </div>

                    {{-- 日付 --}}
                    <div class="table-row">
                        <span class="label">日付</span>
                        <div class="date">
                            <span class="date-year">{{ \Carbon\Carbon::parse($attendance->date)->format('Y年') }}</span>
                            <span class="date-day">{{ \Carbon\Carbon::parse($attendance->date)->format('n月j日') }}</span>
                        </div>
                        <span class="error-message"></span>
                    </div>

                    {{-- 出勤・退勤 --}}
                    <div class="table-row">
                        <span class="label">出勤・退勤</span>
                        <div class="time">
                            <input class="time-box" type="time" name="start_time"
                                value="{{ \Carbon\Carbon::parse($attendance->start_time)->format('H:i') }}" />〜
                            <input class="time-box" type="time" name="end_time"
                                value="{{ \Carbon\Carbon::parse($attendance->end_time)->format('H:i') }}" />
                        </div>

                        {{-- エラーメッセージ --}}
                        <span class="error-message">
                            @if ($errors->has('start_time') || $errors->has('end_time'))
                                <p>
                                    {{ $errors->first('start_time') ?? $errors->first('end_time') }}
                                </p>
                            @endif
                        </span>
                    </div>

                    {{-- 休憩時間 --}}
                    @foreach ($attendance->breaks as $break)
                        <div class="table-row">
                            <span class="label">休憩</span>
                            <div class="time">
                                <input class="time-box" type="time" name="breaks[{{ $break->id }}][start]"
                                    value="{{ \Carbon\Carbon::parse($break->break_start_time)->format('H:i') }}" />〜
                                <input class="time-box" type="time" name="breaks[{{ $break->id }}][end]"
                                    value="{{ \Carbon\Carbon::parse($break->break_end_time)->format('H:i') }}" />
                            </div>
                            {{-- エラーメッセージ --}}
                            <span class="error-message">
                                {{-- 休憩開始時間もしくは休憩終了時間が不適切な値です --}}
                                @if ($errors->has("breaks.$break->id.invalid_time"))
                                    <p>{{ $errors->first("breaks.$break->id.invalid_time") }}</p>
                                @endif
                                {{-- 休憩時間が勤務時間外です --}}
                                @if ($errors->has("breaks.$break->id.outside_working_hours"))
                                    <p>{{ $errors->first("breaks.$break->id.outside_working_hours") }}</p>
                                @endif
                            </span>
                        </div>
                    @endforeach

                    {{-- 備考 --}}
                    <div class="table-row">
                        <span class="label">備考</span>
                        <div class="reason">
                            <textarea class="reason__input" name="reason" placeholder="修正理由"></textarea>
                        </div>
                        {{-- エラーメッセージ --}}
                        <span class="error-message">
                            @if ($errors->has('reason'))
                                <p>{{ $errors->first('reason') }}</p>
                            @endif
                        </span>
                    </div>
                </div>
                <button class="edit-button">修正</button>
            </form>
        </div>
    </div>
@endsection
