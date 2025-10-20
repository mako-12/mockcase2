@extends('layouts.default')

@section('title', '管理者用ログイン')


@section('content')

    @include('components.header')

    <form action="/admin/login" method="post" class="authenticate center">
        @csrf
        <hi class="page_title">管理者ログイン</hi>
        <label for="mail" class="entry__name">メールアドレス</label>
        <input type="email" name="email" id="mail" class="input" value="{{ old('email') }}">
        <div class="form__error">
            @error('email')
                {{ $message }}
            @enderror
        </div>
        <label for="password" class="entry__name">パスワード</label>
        <input type="password" name="password" id="password">
        <div class="form__error">
            @error('password')
                {{ $message }}
            @enderror
        </div>
        <button class="btn btn--big">ログインする</button>
    </form>
@endsection
