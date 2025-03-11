@extends('layouts.admin-app')

@section('title', '従業員一覧画面（管理者）')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/attendance/admin/employee-list.css') }}">
@endpush

@section('content')
    <div class="container-wrap">
        <div class="container">
            <h1 class="title">スタッフ一覧</h1>
            <div class="attendance-table">
                <div class="table-header">
                    <span class="table-header-name">名前</span>
                    <span class="table-header-mail">メールアドレス</span>
                    <span class="table-header-detail">月次退勤</span>
                </div>

                {{-- employeeデータを繰り返し --}}
                @foreach ($employees as $employee)
                    <div class="table-row">

                        {{-- 名前 --}}
                        <span class="name">{{ $employee->name }}</span>

                        {{-- メールアドレス --}}
                        <span class="mail">{{ $employee->email }}</span>

                        {{-- 詳細リンク --}}
                        <a href="{{ route('admin.attendance.monthly-list', ['employeeId' => $employee->id]) }}"
                            class="detail">詳細</a>
                    </div>
                @endforeach

            </div>
        </div>
    </div>
@endsection
