@extends('layouts.common')

@section('title', 'ログイン')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth/employee/login.css') }}">
@endpush

@section('content')
    <div class="container-wrap">
        <div class="container">
            <h1 class="title-center">ログイン</h1>
            {{-- メッセージ表示 --}}
            <div class="message">
                @if (session()->has('error'))
                    <p>{{ session()->get('error') }}</p>
                @endif
            </div>
            <form class="form" method="POST" action="{{ route('employee.authenticate') }}">
                @csrf
                <div class="form-group">
                    <label class="form-group__label" for="email">メールアドレス</label>
                    <div>
                        {{-- エラーメッセージ --}}
                        @if ($errors->has('email'))
                            <div class="error-message">
                                <ul>
                                    @foreach ($errors->get('email') as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                    <input class="form-group__input" type="text" id="email" name="email" />
                </div>
                <div class="form-group">
                    <label class="form-group__label" for="password">パスワード</label>
                    <div>
                        {{-- エラーメッセージ --}}
                        @if ($errors->has('password'))
                            <div class="error-message">
                                <ul>
                                    @foreach ($errors->get('password') as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                    <input class="form-group__input" type="password" id="password" name="password" />
                </div>
                <button type="submit" class="form-group__submit-btn">ログインする</button>
            </form>
            <p class="login-link">
                <a class="login-link__link-btn" href="{{ route('employee.register') }}">会員登録はこちら</a>
            </p>
        </div>
    </div>
@endsection
