@extends('layouts.default')

@section('title', '会員登録画面')
@section('body-class', 'register-page')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/authentication.css') }}">
@endsection

@section('content')
    @include('components.header')
    <div class="content">
        <form action="/register" method="post" class="inner">
            @csrf
            <h1 class="page__title">会員登録</h1>
            <label for="name" class="entry__name">名前</label>
            <input type="text" name="name" id="name" class="input" value="{{ old('name') }}">
            <div class="error-message">
                @error('name')
                    {{ $message }}
                @enderror
            </div>
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
            <label for="password_confirm" class="entry__name">バスワード確認</label>
            <input type="password" name="password_confirmation" id="password_confirm" class="input">


            <button class="btn btn--big">登録する</button>
            <a href="/login" class="link">ログインはこちら</a>
        </form>
    </div>
@endsection
