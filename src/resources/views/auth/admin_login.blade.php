@extends('layouts.default')
@section('title', '管理者用ログイン')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/authentication.css') }}">
@endsection
@section('body-class', 'login-page')

@section('content')



    @include('components.header')

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="content">
        <form action="/admin/login" method="post" class="inner">
            @csrf
            <h1 class="page__title">管理者ログイン</h1>

            <div class="page__item">
                <label for="mail" class="entry__name">メールアドレス</label>
                <input type="email" name="email" id="email" class="input" value="{{ old('email') }}">
                <div class="error-message">
                    @error('email')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="page__item">
                <label for="password" class="entry__name">パスワード</label>

                <input type="password" name="password" id="password" class="input">
                <div class="error-message">
                    @error('password')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="page__item">
                <button class="btn btn--big">管理者ログインする</button>
            </div>
        </form>
    </div>
@endsection
