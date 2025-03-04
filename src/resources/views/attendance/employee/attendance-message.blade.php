@extends('layouts.employee-app-1-2-1')

@section('title', '勤怠登録登録画面（一般ユーザー）')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/attendance/employee/attendance-message.css') }}">
@endpush

@section('content')
    <div class="container-wrap">
        <div class="container">
            <div class="form-group">
                <p class="work-status"> 退勤済</p>
                <p class="date" id="current-date"></p>
                <p class="time" id="current-time"></p>
                {{-- メッセージ表示 --}}
                <div class="message">
                    <p>{{ session()->get('message') }}</p>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/attendance-create.js') }}"></script>
@endsection
