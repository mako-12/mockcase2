@extends('layouts.default')

@section('title', '一般用ログイン')
@section('body-class', 'login-page')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/authentication.css') }}">
@endsection

@section('content')
    @include('components.header')
    <div class="content">
        <form action="/login" method="post" class="inner">
            @csrf
            <h1 class="page__title">ログイン</h1>

            @if (session('error'))
                <div class="error-message">
                    {{ session('error') }}
                </div>
            @endif

            <label for="mail" class="entry__name">メールアドレス</label>
            <input type="email" name="email" id="mail" class="input" value="{{ old('email') }}">
            <div class="error-message">
                @error('email')
                    {{ $message }}
                @enderror
            </div>
            <label for="password" class="entry__name">パスワード</label>
            <input type="password" name="password" id="password" class="input">
            <div class="error-message">
                @error('password')
                    {{ $message }}
                @enderror
            </div>

            <button class="btn btn--big">ログインする</button>
            <a href="/register" class="link">会員登録はこちら</a>
        </form>
    </div>
@endsection
