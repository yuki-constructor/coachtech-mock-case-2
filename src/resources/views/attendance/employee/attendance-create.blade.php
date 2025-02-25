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
                    <p class="work-status">勤務中</p>
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

    <script>
        function updateDateTime() {
            const now = new Date();

            // 日本語の曜日を取得
            const dayNames = ['日', '月', '火', '水', '木', '金', '土'];
            const year = now.getFullYear();
            const month = now.getMonth() + 1;
            const date = now.getDate();
            const day = dayNames[now.getDay()];

            // 日付を表示
            document.getElementById('current-date').textContent = `${year}年${month}月${date}日 (${day})`;

            // 時刻を表示
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');

            document.getElementById('current-time').textContent = `${hours}:${minutes}`;
        }

        updateDateTime(); // 初回実行
        setInterval(updateDateTime, 1000); // 1秒ごとに更新
    </script>

@endsection
