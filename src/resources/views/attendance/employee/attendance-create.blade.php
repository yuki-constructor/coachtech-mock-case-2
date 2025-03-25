@extends('layouts.employee-app')

@section('title', '勤怠登録登録画面（一般ユーザー）')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/attendance/employee/attendance-create.css') }}">
@endpush

@section('content')
    <div class="container-wrap">
        <div class="container">
            <div class="form-group">
                @if (!$attendance || $attendance->status->status === '勤務外')
                    <p class="work-status">勤務外</p>
                    <p class="date" id="current-date"></p>
                    <p class="time" id="current-time"></p>
                    <form action="{{ route('attendance.clock-in') }}" method="POST">
                        @csrf
                        <button class="form-group__submit-btn">出勤</button>
                    </form>
                @elseif ($attendance->status->status === '勤務中')
                    <p class="work-status">出勤中</p>
                    <p class="date" id="current-date"></p>
                    <p class="time" id="current-time"></p>
                    <div class="form-group__submit-btn--container">
                        <form action="{{ route('attendance.clock-out') }}" method="POST">
                            @csrf
                            <button class="form-group__submit-btn">退勤</button>
                        </form>
                        <form action="{{ route('attendance.break-start') }}" method="POST">
                            @csrf
                            <button class="form-group__submit-btn--white">休憩入</button>
                        </form>
                    </div>
                @elseif ($attendance->status->status === '休憩中')
                    <p class="work-status">休憩中</p>
                    <p class="date" id="current-date"></p>
                    <p class="time" id="current-time"></p>
                    <form action="{{ route('attendance.break-end') }}" method="POST">
                        @csrf
                        <button class="form-group__submit-btn--white">休憩戻</button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <script src="{{ asset('js/attendance-create.js') }}"></script>

@endsection
