@extends('layouts.common')

@section('title', 'メール認証誘導')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth/employee/email-authentication-invitation.css') }}">
@endpush

@section('content')
    <div class="container-wrap">
        <div class="container">
            {{-- エラーメッセージ --}}
            @if (session('error'))
            <p class="error-message">{{ session('error') }}</p>
        @endif
            <p class="message">登録していただいたメールアドレスに認証メールを送付しました。</p>
            <p class="message">メール認証を完了してください。</p>
            <div class="mail-check-link">
                <a class="mail-check-link__btn" href="http://localhost:8025">認証はこちらから</a>
            </div>
            <div class="send-mail-link">
                <form action="{{ route('verification.resend', ['employeeId' => $employee->id]) }}" method="POST">
                    @csrf
                    <button type="submit" class="send-mail-link__btn">
                        認証メールを送信する
                    </button>
                </form>
            </div>
            </p>
        </div>
    </div>
@endsection
