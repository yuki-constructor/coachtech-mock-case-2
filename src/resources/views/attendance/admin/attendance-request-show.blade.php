@extends('layouts.admin-app')

@section('title', '修正申請承認画面（管理者）')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/attendance/admin/attendance-request-show.css') }}">
@endpush

@section('content')
    <div class="container-wrap">
        <div class="container">
            <h1 class="title">勤怠詳細</h1>
            <form
                action="{{ route('admin.attendance.request.acknowledge', ['attendanceRequestId' => $attendanceRequest->id]) }}"
                method="POST">
                @csrf
                <div class="attendance-table">

                    {{-- 名前 --}}
                    <div class="table-row">
                        <span class="label">名前</span>
                        <span class="name">{{ $attendanceRequest->attendance->employee->name }}</span>
                        <span class="error-message"></span>
                    </div>

                    {{-- 日付 --}}
                    <div class="table-row">
                        <span class="label">日付</span>
                        <div class="date">
                            <span
                                class="date-year">{{ \Carbon\Carbon::parse($attendanceRequest->attendance->date)->format('Y年') }}</span>
                            <span
                                class="date-day">{{ \Carbon\Carbon::parse($attendanceRequest->attendance->date)->format('n月j日') }}</span>
                        </div>
                        <span class="error-message"></span>
                    </div>

                    {{-- 出勤・退勤 --}}
                    <div class="table-row">
                        <span class="label">出勤・退勤</span>
                        <div class="time">
                            <span
                                class="time-box">{{ \Carbon\Carbon::parse($attendanceRequest->start_time)->format('H:i') }}</span>
                            〜
                            <span
                                class="time-box">{{ \Carbon\Carbon::parse($attendanceRequest->end_time)->format('H:i') }}</span>
                        </div>
                    </div>
                    <span class="error-message">
                    </span>

                    {{-- 休憩時間 --}}
                    @foreach ($attendanceRequest->attendanceRequestBreaks as $attendanceRequestBreak)
                        <div class="table-row">
                            <span class="label">休憩</span>
                            <div class="time">
                                <span
                                    class="time-box">{{ \Carbon\Carbon::parse($attendanceRequestBreak->break_start_time)->format('H:i') }}</span>
                                〜
                                <span
                                    class="time-box">{{ \Carbon\Carbon::parse($attendanceRequestBreak->break_end_time)->format('H:i') }}</span>
                            </div>
                            <span class="error-message">
                            </span>
                        </div>
                    @endforeach

                    {{-- 備考 --}}
                    <div class="table-row">
                        <span class="label">備考</span>
                        <div class="reason">
                            <span>{{ $attendanceRequest->reason }}</span>
                        </div>
                        <span class="error-message">
                        </span>
                    </div>
                </div>
                @if ($attendanceRequest->attendance_request_status_id === ($approvedStatusId ?? null))
                    <button class="approved-button" disabled>承認済み</button>
                @else
                    <button class="edit-button">承認</button>
                @endif
            </form>
        </div>
    </div>
    </div>
@endsection
