@extends('layouts.admin-app')

@section('title', '従業員別月次勤怠一覧画面（管理者）')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/attendance/admin/attendance-monthly-list.css') }}">
@endpush

@section('content')
    <div class="container-wrap">
        <div class="container">
            <h1 class="title">{{ $employee->name }}さんの勤怠</h1>
            <div class="month-navigation">
                <a
                    href="{{ route('admin.attendance.monthly-list', ['employeeId' => $employee->id, 'month' => \Carbon\Carbon::parse($month)->subMonth()->format('Y-m')]) }}">&larr;
                    前月</a>
                <div class="month-navigation-center">
                    <img class="month-navigation-calendar__image" src="{{ asset('storage/photos/logo_images/calendar.png') }}"
                        alt="カレンダー" />
                    <span class="month">{{ \Carbon\Carbon::parse($month)->format('Y/m') }}</span>
                </div>
                <a
                    href="{{ route('admin.attendance.monthly-list', ['employeeId' => $employee->id, 'month' => \Carbon\Carbon::parse($month)->addMonth()->format('Y-m')]) }}">翌月
                    &rarr;</a>
            </div>

            <div class="attendance-table">
                <div class="table-header">
                    <span>日付</span>
                    <span>出勤</span>
                    <span>退勤</span>
                    <span>休憩</span>
                    <span>合計</span>
                    <span>詳細</span>
                </div>

                @foreach ($attendances as $attendance)
                    <div class="table-row">
                        <span>{{ \Carbon\Carbon::parse($attendance->date)->locale('ja')->isoFormat('MM/DD (ddd)') }}</span>

                        {{-- 出勤時刻 --}}
                        <span>{{ $attendance->start_time ? \Carbon\Carbon::parse($attendance->start_time)->format('H:i') : '-' }}</span>

                        {{-- 退勤時刻 --}}
                        <span>{{ $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '-' }}</span>

                        {{-- 休憩時間の合計 --}}
                        <span>{{ $attendance->total_break_time }}</span>

                        {{-- 勤務時間の合計 --}}
                        <span>{{ $attendance->total_work_time }}</span>

                        {{-- 詳細リンク --}}
                        <a href="{{ route('admin.attendance.show', ['attendanceId' => $attendance->id]) }}">詳細</a>
                    </div>
                @endforeach

            </div>
            <a href="" class="csv-button">
                CSV出力
            </a>
        </div>
    </div>
@endsection
