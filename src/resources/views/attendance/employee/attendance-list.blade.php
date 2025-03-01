@extends('layouts.employee-app')

@section('title', '勤怠一覧画面（従業員）')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/attendance/employee/attendance-list.css') }}">
@endpush

@section('content')
    <div class="container-wrap">
        <div class="container">
            <h1>勤怠一覧</h1>
            <div class="month-navigation">
                <a
                    href="{{ route('employee.attendance.list', ['month' => \Carbon\Carbon::parse($month)->subMonth()->format('Y-m')]) }}">&larr;
                    前月</a>
                <div class="month-navigation-center">
                    <img class="month-navigation-calendar__image" src="{{ asset('storage/photos/logo_images/calendar.png') }}"
                        alt="カレンダー" />
                    <span class="month">{{ \Carbon\Carbon::parse($month)->format('Y/m') }}</span>
                </div>
                <a
                    href="{{ route('employee.attendance.list', ['month' => \Carbon\Carbon::parse($month)->addMonth()->format('Y-m')]) }}">翌月
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
                        <span>{{ $attendance->start_time ? \Carbon\Carbon::parse($attendance->start_time)->format('H:i') : '-' }}</span>
                        <span>{{ $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '-' }}</span>
                        <!-- 休憩時間の合計を計算 -->
                        <span>
                            @php
                                $totalBreakMinutes = $attendance->breaks->sum(function ($break) {
                                    if ($break->break_end_time) {
                                        return \Carbon\Carbon::parse($break->break_start_time)->diffInMinutes(
                                            \Carbon\Carbon::parse($break->break_end_time),
                                        );
                                    }
                                    return 0;
                                });
                                echo floor($totalBreakMinutes / 60) .
                                    ':' .
                                    str_pad($totalBreakMinutes % 60, 2, '0', STR_PAD_LEFT);
                            @endphp
                        </span>
                        <!-- 勤務時間の合計を計算 -->
                        <span>
                            @if ($attendance->start_time && $attendance->end_time)
                                @php
                                    $workMinutes =
                                        \Carbon\Carbon::parse($attendance->start_time)->diffInMinutes(
                                            $attendance->end_time,
                                        ) - $totalBreakMinutes;
                                    echo floor($workMinutes / 60) .
                                        ':' .
                                        str_pad($workMinutes % 60, 2, '0', STR_PAD_LEFT);
                                @endphp
                            @else
                                -
                            @endif
                        </span>
                        <a href="{{ route('employee.attendance.show', ['attendanceId' => $attendance->id]) }}">詳細</a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
