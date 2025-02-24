@extends('layouts.common')

@section('title', 'ログイン')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth/employee/login.css') }}">
@endpush

@section('content')
    <div class="container-wrap">
        <div class="container">
            <h1 class="title">ログイン</h1>
            <form class="form">
                <div class="form-group">
                    <label class="form-group__label" for="username">メールアドレス</label>
                    <input class="form-group__input" type="text" id="username" name="username" required />
                </div>
                <div class="form-group">
                    <label class="form-group__label" for="password">パスワード</label>
                    <input class="form-group__input" type="password" id="password" name="password" required />
                </div>
                <button type="submit" class="form-group__submit-btn">ログインする</button>
            </form>
            <p class="login-link">
                <a class="login-link__link-btn" href="{{ route('employee.register') }}">会員登録はこちら</a>
            </p>
        </div>
    </div>
@endsection
