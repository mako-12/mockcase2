@extends('layouts.default')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
    @include('components.header')
    <div class="page__header">
        <h2>勤怠詳細</h2>
    </div>


    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif



    <form action="{{ route('attendance.request.submit', $attendance->id) }}" method="post">
        @csrf
        <table class="detail__table">
            <tr class="detail__row">
                <th class="detail__label">名前</th>
                <td class="detail__data">{{ $attendance->user->name }}</td>
            </tr>

            <tr class="detail__row">
                <th class="detail__label">日付</th>
                <td class="detail__data">
                    <span class="detail__data-year">
                        {{ $attendance->work_date->format('Y') }}年
                    </span>
                    <span class="detail__data-day">
                        {{ $attendance->work_date->format('n月j日') }}
                    </span>
                </td>
            </tr>

            <tr class="detail__row">
                <th class="detail__label">出勤・退勤</th>
                <td class="detail__data">
                    {{-- <input type="time" class="detail__input" name="start_time"
                        value="{{ old('start_time', $attendance->start_time ? $attendance->start_time->format('H:i') : '') }}"
                        {{ $pendingRequest ? 'disabled' : '' }} required> --}}
                    <input type="time" class="detail__input" name="start_time"
                        value="{{ $pendingRequest && $pendingRequest->requested_start_time
                            ? $pendingRequest->requested_start_time->format('H:i')
                            : ($attendance->start_time
                                ? $attendance->start_time->format('H:i')
                                : '') }}"
                        {{ $pendingRequest ? 'disabled' : '' }} required>
                    <span class="detail__separator">～</span>
                    {{-- <input type="time" class="detail__input" name="end_time"
                        value="{{ old('end_time', $attendance->end_time ? $attendance->end_time->format('H:i') : '') }}"
                        {{ $pendingRequest ? 'disabled' : '' }} required> --}}

                    <input type="time" class="detail__input" name="end_time"
                        value="{{ $pendingRequest && $pendingRequest->requested_end_time
                            ? $pendingRequest->requested_end_time->format('H:i')
                            : ($attendance->end_time
                                ? $attendance->end_time->format('H:i')
                                : '') }}"
                        {{ $pendingRequest ? 'disabled' : '' }} required>

                </td>
            </tr>

            @foreach ($attendance->breakTimes as $index => $breakTime)
                @php
                    //承認待ちの休憩申請を取得
                    $pendingBreak = $pendingRequest
                        ? $pendingRequest->breakRequests->where('break_time_id', $breakTime->id)->first()
                        : null;

                    //表示する値を決定(承認待ち優先、なければ現在の値)
                    $breakStart = '';
                    if ($pendingBreak && $pendingBreak->requested_break_start) {
                        $breakStart = $pendingBreak->requested_break_start->format('H:i');
                    } elseif ($breakTime->break_start) {
                        $breakStart = $breakTime->break_start->format('H:i');
                    }

                    $breakEnd = '';
                    if ($pendingBreak && $pendingBreak->requested_break_end) {
                        $breakEnd = $pendingBreak->requested_break_end->format('H:i');
                    } elseif ($breakTime->break_end) {
                        $breakEnd = $breakTime->break_end->format('H:i');
                    }
                @endphp
                <tr class="detail__row">
                    <th class="detail__label">休憩</th>
                    <td class="detail__data">
                        <input type="time" class="detail__input" name="break_start[]" value="{{ $breakStart }}"
                            {{ $pendingRequest ? 'disabled' : '' }}>
                        <span class="detail__separator">～</span>
                        <input type="time" class="detail__input" name="break_end[]" value="{{ $breakEnd }}"
                            {{ $pendingRequest ? 'disabled' : '' }}>
                    </td>
                </tr>
            @endforeach

            {{-- 新規追加用の休憩(承認待ちでない場合のみに表示) --}}
            {{-- @if (!$pendingRequest) --}}
            @php
                //承認待ちのbreak_time_idがnullの申請(新規追加の休憩)を取得
                $newBreaks = $pendingRequest ? $pendingRequest->breakRequests->whereNull('break_time_id') : collect();
            @endphp

            @if ($newBreaks->isNotEmpty())
                @foreach ($newBreaks as $index => $newBreak)
                    <tr class="detail__row">
                        <th class="detail__label">休憩{{ count($attendance->breakTimes) + 1 }}</th>
                        <td class="detail_data">
                            <input type="time" class="detail__input" name="break_start[]"
                                value="{{ $newBreak->requested_break_start ? $newBreak->requested_break_start->format('H:i') : '' }}"
                                {{ $pendingRequest ? 'disabled' : '' }}>
                            <span class="detail__separator">〜</span>
                            <input type="time" class="detail__input" name="break_end[]"
                                value="{{ $newBreak->requested_break_end ? $newBreak->requested_break_end->format('H:i') : '' }}"
                                {{ $pendingRequest ? 'disabled' : '' }}>
                        </td>
                    </tr>
                @endforeach
            @endif
            {{-- 空の追加欄 --}}
            @if (!$pendingRequest)
                <tr class="detail__row">
                    <th class="detail__label">休憩{{ count($attendance->breakTimes) + count($newBreaks) + 1 }}</th>
                    <td class="detail__data">
                        <input type="time" class="detail__input" name="break_start[]">
                        <span class="detail__separator">～</span>
                        <input type="time" class="detail__input" name="break_end[]">
                    </td>
                </tr>
            @endif




            {{-- <tr class="detail__row">
                <th class="detail__label">休憩{{ count($attendance->breakTimes) + 1 }}</th>
                <td class="detail_data">
                    <input type="time" class="detail__input" name="break_start[]"
                        {{ $pendingRequest ? 'disabled' : '' }}>
                    <span class="detail__separator">〜</span>
                    <input type="time" class="detail__input" name="break_end[]" {{ $pendingRequest ? 'disabled' : '' }}>
                </td>
            </tr> --}}


            <tr class="detail__row">
                <th class="detail__label">備考</th>
                <td class="detail__data">
                    <textarea name="reason" rows="2" class="detail__textarea" {{ $pendingRequest ? 'disabled' : '' }} required>{{ $pendingRequest ? $pendingRequest->reason : '' }}</textarea>
            </tr>
        </table>

        <div class="detail__action">
            @if ($pendingRequest)
                <p class="detail__pending-message">*承認待ちのため修正はできません。</p>
            @else
                <button class="detail__submit-btn">修正</button>
            @endif
        </div>
    </form>
@endsection
