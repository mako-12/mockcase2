@extends('layouts.default')

@section('title', '勤怠一覧')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/attendance_list.css') }}">
@endsection

@section('content')
    @include('components.header')
    <div class="content">
        <div class="inner">
            <div class="page__header">
                <h2>{{ \Carbon\Carbon::parse($day)->format('Y年m月d日') }}の勤怠
                </h2>
            </div>



            <div class="month-nav">
                <span class="month-nav__prev">
                    <a href="{{ route('admin.attendance.list', ['day' => \Carbon\Carbon::parse($day)->subDay()->format('Y-m-d')]) }}"
                        class="month-nav__prev">← 前日</a>
                </span>
                <span class="month-nav__current">
                    <i class="fa-solid fa-calendar-days"></i>
                    {{ \Carbon\Carbon::parse($day)->format('Y/m/d') }}
                </span>
                <span class="month-nax__next">
                    <a href="{{ route('admin.attendance.list', ['day' => \Carbon\Carbon::parse($day)->addDay()->format('Y-m-d')]) }}"
                        class="mont-nav__next">翌日 →</a>
                </span>
            </div>

            <table class="attendance-list__table">
                <tr class="attendance-list__row">
                    <th class="attendance-list__label">
                        名前
                    </th>
                    <th class="attendance-list__label">
                        出勤
                    </th>
                    <th class="attendance-list__label">
                        退勤
                    </th>
                    <th class="attendance-list__label">
                        休憩
                    </th>
                    <th class="attendance-list__label">
                        合計
                    </th>
                    <th class="attendance-list__label">
                        詳細
                    </th>
                </tr>

                @foreach ($attendances as $attendance)
                    <tr class="attendance-list__row">
                        <td class="attendance-list__data">
                            {{ $attendance->user->name }}
                        </td>
                        <td class="attendance-list__data">
                            {{ $attendance->start_time ? \Carbon\Carbon::parse($attendance->start_time)->format('H:i') : '' }}
                        </td>
                        <td class="attendance-list__data">
                            {{ $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '' }}
                        </td>
                        <td class="attendance-list__data">
                            {{ $attendance->break_duration_format }}
                        </td>
                        <td class="attendance-list__data">
                            {{ $attendance->work_duration_format }}
                        </td>
                        <td class="attendance-list__data">
                            <div class="disable">
                                <a href="{{ route('admin.attendance.detail', ['id' => $attendance->id]) }}">詳細</a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection
