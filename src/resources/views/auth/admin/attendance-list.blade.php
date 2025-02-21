@extends('layouts.admin-app')

@section('title', '勤怠一覧画面（管理者）')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth/admin/attendance-list.css') }}">
@endpush

@section('content')
    <div class="container-wrap">
        <div class="container">
            <h1>2023年6月1日の勤怠</h1>
            <div class="month-navigation">
                <button>&larr; 前月</button>
                <div class="month-navigation-center">
                    <img class="month-navigation-calendar__image" src="calendar.png" alt="" />
                    <span class="month">2023/06</span>
                </div>
                <button>翌月 &rarr;</button>
            </div>

            <div class="attendance-table">
                <div class="table-header">
                    <span class="name">名前</span>
                    <span>出勤</span>
                    <span>退勤</span>
                    <span>休憩</span>
                    <span>合計</span>
                    <span class="detail">詳細</span>
                </div>

                <!-- 勤怠データを繰り返し -->
                <div class="table-row">
                    <span class="name">山田　太郎</span>
                    <span>09:00</span>
                    <span>18:00</span>
                    <span>1:00</span>
                    <span>8:00</span>
                    <a class="detail" href="#">詳細</a>
                </div>

                <div class="table-row">
                    <span class="name">西　玲奈</span>
                    <span>09:00</span>
                    <span>18:00</span>
                    <span>1:00</span>
                    <span>8:00</span>
                    <a class="detail" href="#">詳細</a>
                </div>
            </div>
        </div>
    </div>
@endsection
