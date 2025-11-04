@extends('layouts.default')

@section('title', 'スタッフ別勤怠一覧')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/attendance_list.css') }}">
@endsection

@section('content')
    @include('components.header')

    <div class="page__header">
        <h2>{{ $attendances->user->name }}さんの勤怠</h2>
    </div>

    <div class="month-nav">

        <div class="month-nav__current">
            {{-- {{ Carbon::today()->format('H年m月') }} --}}
        </div>

    </div>


@endsection
