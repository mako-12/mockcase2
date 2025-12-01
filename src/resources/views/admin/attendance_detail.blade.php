@extends('layouts.default')

@section('title', '勤怠詳細画面')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
    @include('components.header')

    <div class="content">
        <div class="inner">
            <div class="page__header">
                <h2>勤怠詳細</h2>
            </div>

            <form action="{{ route('admin.attendance.update', $attendance->id) }}" method="post">
                @csrf
                <div class="table-wrapper">
                    <table class="detail__table">
                        <tr class="detail__row">
                            <th class="detail__label">名前</th>
                            <td class="detail__data">
                                {{ $attendance->user->name }}
                            </td>
                        </tr>

                        <tr class="detail__row">
                            <th class="detail__label">日付</th>
                            <td class="detail__data">
                                <span class="detail_data-year">
                                    {{ $attendance->work_date->format('Y年') }}
                                </span>
                                <span class="detail__data-day">
                                    {{ $attendance->work_date->format('n月j日') }}
                                </span>
                            </td>
                        </tr>

                        <tr class="detail__row">
                            <th class="detail__label">出勤・退勤</th>
                            <td class="detail__data">
                                <input type="time" name="start_time" class="detail__input"
                                    value="{{ $attendance->start_time ? $attendance->start_time->format('H:i') : '' }}"
                                    {{ $pendingRequest ? 'disabled' : '' }}>
                                <span class="detail__separator">～</span>
                                <input type="time" name="end_time" class="detail__input"
                                    value="{{ $attendance->end_time ? $attendance->end_time->format('H:i') : '' }}"
                                    {{ $pendingRequest ? 'disabled' : '' }}>

                                <div class="error-message">
                                    @error('end_time')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </td>
                        </tr>


                        {{-- 既存と修正の休憩時間を表示するための準備処理 --}}
                        {{-- @foreach ($attendance->breakTimes as $index => $breakTime)
            @php
                $pendingBreak = $pendingRequest?$pendingRequest->breakRequests->
            @endphp --}}

                        {{-- 1つ目の休憩 --}}
                        @foreach ($attendance->breakTimes as $index => $breakTime)
                            <tr class="detail__row">
                                <th class="detail__label">{{ $index === 0 ? '休憩' : '休憩' . ($index + 1) }}</th>
                                <td class="detail__data">
                                    <input type="time" name="break_start[]" class="detail__input"
                                        value="{{ $breakTime->break_start ? $breakTime->break_start->format('H:i') : '' }}"
                                        {{ $pendingRequest ? 'disabled' : '' }}>


                                    <span class="detail__separator">～</span>
                                    <input type="time" name="break_end[]" class="detail__input"
                                        value="{{ $breakTime->break_end ? $breakTime->break_end->format('H:i') : '' }}"
                                        {{ $pendingRequest ? 'disabled' : '' }}>

                                    {{-- @if ($breakTime->break_start && $breakTime->break_end && $index == 0) --}}
                                    <div class="error-message">
                                        @error("break_end.$index")
                                            <p>{{ $message }}</p>
                                        @enderror

                                        @error("break_start.$index")
                                            <p>{{ $message }}</p>
                                        @enderror
                                    </div>
                                    {{-- @endif --}}
                                </td>
                            </tr>
                        @endforeach


                        {{-- 新規追加用の休憩(承認待ちでない場合のみに表示) --}}
                        @php
                            //承認待ちのbreak_time_idがnullの申請(新規追加の休憩)を取得
                            $newBreaks = $pendingRequest
                                ? $pendingRequest->breakRequests->whereNull('break_time_id')
                                : collect();
                        @endphp

                        @if ($newBreaks->isNotEmpty())
                            @foreach ($newBreaks as $index => $newBreak)
                                <tr class="detail__row">
                                    <th class="detail__label">
                                        {{ count($attendance->breakTimes) === 0 && $index === 0
                                            ? '休憩'
                                            : '休憩' . (count($attendance->breakTimes) + 1) }}
                                    </th>
                                    <td class="detail__data">
                                        <input type="time" class="detail__input" name="break_start[]"
                                            value="{{ $newBreak->requested_break_start ? $newBreak->requested_break_start->format('H:i') : '' }}"
                                            {{ $pendingRequest ? 'disabled' : '' }}>
                                        <span class="detail__separator">〜</span>
                                        <input type="time" class="detail__input" name="break_end[]"
                                            value="{{ $newBreak->requested_break_end ? $newBreak->requested_break_end->format('H:i') : '' }} "
                                            {{ $pendingRequest ? 'disabled' : '' }}>

                                        {{-- @if ($breakTime->break_start && $breakTime->break_end && $index >= 1)
                                        @if (!empty(old("break_end . $index")))
                                            <div class="error-message">
                                                @error("break_end.$index")
                                                    <p>{{ $message }}</p>
                                                @enderror

                                                @error("break_start.$index")
                                                    <p>{{ $message }}</p>
                                                @enderror
                                            </div>
                                        @endif
                                    @endif --}}

                                        {{-- 第二候補 --}}
                                        {{-- @if ($breakTime->break_start && $breakTime->break_end && $index > 0)
                                        <div class="error-message">
                                            @error("break_end.$index")
                                                <p>{{ $message }}</p>
                                            @enderror

                                            @error("break_start.$index")
                                                <p>{{ $message }}</p>
                                            @enderror
                                        </div>
                                    @endif --}}

                                        {{-- 第三候補 --}}
                                        @if ($breakTime->break_start && $breakTime->break_end && $index > 0)
                                            <div class="error-message">
                                                @error("break_end . count($attendance->breakTimes)")
                                                    <p>{{ $message }}</p>
                                                @enderror

                                                @error("break_start. count($attendance->breakTimes)")
                                                    <p>{{ $message }}</p>
                                                @enderror
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif


                        {{-- 空の休憩 --}}
                        @if (!$pendingRequest)
                            <tr class="detail__row">
                                <th class="detail__label">休憩{{ count($attendance->breakTimes) + 1 }}</th>
                                <td class="detail__data">
                                    <input type="time" name="break_start[]" class="detail__input">
                                    <span class="detail__separator">～</span>
                                    <input type="time" name="break_end[]" class="detail__input">

                                    @if (!empty(old('break_end')))
                                        <div class="error-message">
                                            @error('break_end')
                                                <p>{{ $message }}</p>
                                            @enderror
                                            </br>
                                            @error('break_start')
                                                <p>{{ $message }}</p>
                                            @enderror
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endif

                        <tr class="detail__row">
                            <th class="detail__label">備考</th>
                            <td class="detail__data">
                                <textarea name="reason" rows="2" {{ $pendingRequest ? 'disabled' : '' }}>{{ $pendingRequest ? $pendingRequest->reason : '' }}{{ old('reason') }}</textarea>
                                <div class="error-message">
                                    @error('reason')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                @if ($pendingRequest)
                    <p>*承認待ちの為修正できません。</p>
                @else
                    <div class="submit">
                        <button type="submit" class="detail__submit-btn">修正</button>
                    </div>
                @endif
            </form>
        </div>
    </div>

@endsection
