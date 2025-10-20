@extends('layouts.default')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/attendance_list.css') }}">
@endsection

@section('content')
    @include('components.header')
    <div class="page_header">
        <h2>勤怠一覧</h2>
    </div>
    <div class="month_nav">
        <a href="{{ route('attendance.list', ['month' => \Carbon\Carbon::parse($month)->subMonth()->format('Y-m')]) }}"
            class="month_nav__prev"><span class="arrow">←</span>
            前月</a>
        <div class="month_nav--center">
            <span class="month_nav__current">{{ \Carbon\Carbon::parse($month)->format('Y/m') }}</span>
        </div>
        <a href="{{ route('attendance.list', ['month' => \Carbon\Carbon::parse($month)->addMonth()->format('Y-m')]) }}"
            class="month_nav__next"><span class="arrow">→</span>
            翌月</a>
    </div>

    <table class="attendance_table">
        <tr class="table_title">
            <th class="table_title">日付</th>
            <th class="table_title">出勤</th>
            <th class="table_title">退勤</th>
            <th class="table_title">休憩</th>
            <th class="table_title">合計</th>
            <th class="table_title">詳細</th>
        </tr>

        @foreach ($attendances as $attendance)
            <tr class="attendance_row">
                <td class="attendance_date">{{ \Carbon\Carbon::parse($attendance->work_date)->isoFormat('MM/DD(ddd)') }}
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
                </td>
            </tr>
        @endforeach
    </table>
@endsection
