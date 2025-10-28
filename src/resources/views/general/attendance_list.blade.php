@extends('layouts.default')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/attendance_list.css') }}">
@endsection

@section('content')
    @include('components.header')
    <div class="page__header">
        <h2>勤怠一覧</h2>
    </div>
    <div class="month__nav">
        <a href="{{ route('attendance.list', ['month' => \Carbon\Carbon::parse($month)->subMonth()->format('Y-m')]) }}"
            class="month_nav__prev"><span class="arrow">←</span>
            前月</a>
        <div class="month__nav--center">
            <span class="month_nav__current">{{ \Carbon\Carbon::parse($month)->format('Y/m') }}</span>
        </div>
        <a href="{{ route('attendance.list', ['month' => \Carbon\Carbon::parse($month)->addMonth()->format('Y-m')]) }}"
            class="month__nav__next"><span class="arrow">→</span>
            翌月</a>
    </div>

    <table class="attendance__table">
        <tr class="attendance__row">
            <th class="attendance__label">日付</th>
            <th class="attendance__label">出勤</th>
            <th class="attendance__label">退勤</th>
            <th class="attendance__label">休憩</th>
            <th class="attendance__label">合計</th>
            <th class="attendance__label">詳細</th>
        </tr>

        @foreach ($attendances as $attendance)
            <tr class="attendance__row">
                {{-- <td class="attendance_date">{{ \Carbon\Carbon::parse($attendance->work_date)->isoFormat('MM/DD(ddd)') }}
                </td>

                <td class="attendances_date">
                    {{ \Carbon\Carbon::parse($attendance->start_time)->format('H:i') }}
                </td>

                <td class="attendance_date">
                    {{ \Carbon\Carbon::parse($attendance->end_time)->format('H:i') }}
                </td>

                <td class="attendance_date">
                    {{ $attendance->break_duration_format }}
                </td>

                <td class="attendance_date">
                    {{ $attendance->work_duration_format }}
                </td>

                <td class="attendance_date">
                    <a href="">詳細</a>
                </td> --}}

                <td class="attendance__date">
                    {{ \Carbon\Carbon::parse($attendance->work_date)->isoFormat('MM/DD(ddd)') }}
                </td>

                <!-- 勤務データがない場合は空欄 -->
                <td class="attendance__date">
                    {{ $attendance->start_time ? $attendance->start_time->format('H:i') : '' }}
                </td>

                <td class="attendance__date">
                    {{ $attendance->end_time ? $attendance->end_time->format('H:i') : '' }}
                </td>

                <td class="attendance__date">
                    {{ $attendance->break_duration_format ?? '' }}
                </td>

                <td class="attendance__date">
                    {{ $attendance->work_duration_format ?? '' }}
                </td>

                <td class="attendance__date">
                    @if ($attendance->start_time)
                        <a href="{{ route('attendance.detail', ['id' => $attendance->id]) }}">詳細</a>
                    @else
                        <span class="disabled">詳細</span>
                    @endif
                </td>

            </tr>
        @endforeach
    </table>
@endsection
