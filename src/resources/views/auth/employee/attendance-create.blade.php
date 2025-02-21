@extends('layouts.employee-app')

@section('title', '勤怠登録登録画面（一般ユーザー）')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth/employee/attendance-create.css') }}">
@endpush

@section('content')
    <div class="container-wrap">
        <div class="container">
            <div class="form-group">
                <form class="form">
                    <p class="work-status">勤務外</p>
                    <p class="date">2023年6月1日(木)</p>
                    <p class="time">08:00</p>
                    <button type="submit" class="form-group__submit-btn">出勤</button>
                </form>
            </div>
        </div>
    </div>
@endsection
