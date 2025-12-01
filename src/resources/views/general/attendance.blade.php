@extends('layouts.default')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
    @include('components.header')
    <div class="content">
        <div class="inner">
            <div class="attendance__page">
                <div class="attendance__content">
                    <div class="status__name">
                        {{ $attendance?->status_name ?? '勤務外' }}
                    </div>
                    <div class="date">
                        {{ \Carbon\Carbon::today()->isoFormat('YYYY年MM月DD日(ddd)') }}
                    </div>
                    <div class="time">
                        {{ now()->format('H:i') }}
                    </div>



                    <form action="{{ route('attendance.update') }}" method="post">
                        @csrf
                        @if (!$attendance)
                            <button name="action" value="start" class="action__btn">出勤</button>
                        @elseif($attendance->status === \App\Models\Attendance::STATUS_WORKING)
                            <button name="action" value="end" class="action__btn">退勤</button>
                            <button name="action" value="break_start" class="action__btn--break">休憩入</button>
                        @elseif($attendance->status === \App\Models\Attendance::STATUS_BREAK)
                            <button name="action" value="break_end" class="action__btn--break">休憩戻</button>
                        @elseif($attendance->status === \App\Models\Attendance::STATUS_FINISHED)
                            <p class="finished-message">お疲れ様でした。</p>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
