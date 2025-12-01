@extends('layouts.default')

@section('title', 'メール認証')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/verify.css') }}">
@endsection

@section('content')
    @include('components.header')
    <div class="content">
        <div class="inner">
            <div class="verify-page">
                <div class="verify-page__message">
                    <p>登録していただいたメールアドレスに認証メールを送付しました。</p>
                    <p>メール認証を完了してください。</p>
                </div>

                <div class="verify-page__btn">
                    <a href="{{ route('verification.check') }}" class="verify__btn">認証はこちらから</a>
                </div>

                <form class="verify-page__btn" method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button class="verify-email__btn">認証メールを再送する</button>
                </form>
            </div>
        </div>
    </div>
@endsection
