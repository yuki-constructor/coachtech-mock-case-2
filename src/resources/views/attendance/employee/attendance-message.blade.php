@extends('layouts.employee-app')

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
