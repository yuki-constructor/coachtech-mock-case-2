@extends('layouts.admin-app')

@section('title', '日次勤怠一覧画面（管理者）')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/attendance/admin/attendance-daily-list.css') }}">
@endpush

@section('content')
    <div class="container-wrap">
        <div class="container">
            <h1 class="title">{{ $date->isoFormat('YYYY年M月D日') }}の勤怠</h1>
            <div class="day-navigation">
                <a href="{{ route('admin.attendance.daily-list', ['date' => $date->copy()->subDay()->toDateString()]) }}">
                    &larr;前日</a>
                <div class="day-navigation-center">
                    <img class="day-navigation-calendar__image" src="{{ asset('storage/photos/logo_images/calendar.png') }}"
                        alt="カレンダー" />
                    <span class="day">{{ $date->format('Y/m/d') }}</span>
                </div>
                <a href="{{ route('admin.attendance.daily-list', ['date' => $date->copy()->addDay()->toDateString()]) }}">
                    翌日&rarr;</a>
            </div>

            <div class="attendance-table">
                <div class="table-header">
                    <span>名前</span>
                    <span>出勤</span>
                    <span>退勤</span>
                    <span>休憩</span>
                    <span>合計</span>
                    <span>詳細</span>
                </div>

                {{-- 勤怠データを繰り返し --}}
                @foreach ($attendances as $attendance)
                    <div class="table-row">
                        <span>{{ $attendance->employee->name }}</span>

                        {{-- 出勤時刻 --}}
                        <span>{{ \Carbon\Carbon::parse($attendance->start_time)->format('H:i') }}</span>

                        {{-- 退勤時刻 --}}
                        <span>{{ $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '-' }}</span>

                        {{-- 休憩時間の合計 --}}
                        <span>{{ $attendance->total_break_time }}</span>

                        {{-- 勤務時間の合計 --}}
                        <span>{{ $attendance->total_work_time }}</span>

                        {{-- 詳細画面へのリンク --}}
                        <a href="{{ route('admin.attendance.show', ['attendanceId' => $attendance->id]) }}">詳細</a>
                    </div>
                @endforeach

            </div>
        </div>
    </div>
@endsection
