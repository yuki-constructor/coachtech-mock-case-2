@extends('layouts.employee-app')

@section('title', '勤怠修正申請一覧（承認済み）画面（従業員）')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/attendance/employee/attendance-request-list-approved.css') }}">
@endpush

@section('content')
    <div class="container-wrap">
        <div class="container">
            <h1 class="title">申請一覧</h1>
            {{-- メニューバー --}}
            <div class="menu">
                <div class="menu__link">
                    <a href="{{ route('employee.attendance.request.list.pending') }}" class="menu__link-left">承認待ち</a>
                    <a class="menu__link-right">承認済み</a>
                </div>
            </div>
            {{-- 申請一覧 --}}
            <div class="attendance-table">
                <div class="table-header">
                    <span>状態</span>
                    <span>名前</span>
                    <span>対象日時</span>
                    <span>申請理由</span>
                    <span>申請日時</span>
                    <span>詳細</span>
                </div>

                @foreach ($attendanceRequests as $request)
                    <div class="table-row">
                        {{-- 承認待ち/承認済み  --}}
                        <span>{{ $request->attendanceRequestStatus->request_status }}</span>
                        {{-- ログイン中の従業員の名前  --}}
                        <span>{{ $employee->name }}</span>
                        {{-- 勤怠日 --}}
                        <span>{{ \Carbon\Carbon::parse($request->attendance->date)->format('Y/m/d') }}</span>
                        {{-- 申請理由 --}}
                        <span>{{ $request->reason }}</span>
                        {{-- 申請日時 --}}
                        <span>{{ \Carbon\Carbon::parse($request->created_at)->format('Y/m/d') }}</span>
                        {{-- 勤怠詳細画面へのリンク --}}
                        <a
                            href="{{ route('employee.attendance.request.show', ['attendanceRequestId' => $request->id]) }}">詳細</a>
                    </div>
                @endforeach

            </div>
        </div>
    </div>
@endsection
