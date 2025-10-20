@extends('layouts.default')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
    @include('components.header')

    <div class="status_name">
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
            <button name="action" value="start">出勤</button>
        @elseif($attendance->status === \App\Models\Attendance::STATUS_WORKING)
            <button name="action" value="break_start">休憩入</button>
            <button name="action" value="end">退勤</button>
        @elseif($attendance->status === \App\Models\Attendance::STATUS_BREAK)
            <button name="action" value="break_end">休憩戻</button>
        @elseif($attendance->status === \App\Models\Attendance::STATUS_FINISHED)
            <p>お疲れ様でした。</p>
        @endif
    </form>
@endsection
