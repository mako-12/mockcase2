@extends('layouts.default')

@section('title', 'スタッフ別勤怠一覧')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/attendance_list.css') }}">
@endsection

@section('content')
    @include('components.header')
    <div class="content">
        <div class="inner">
            <div class="page__header">
                <h2>{{ $user->name }}さんの勤怠</h2>
            </div>
            <div class="month-nav">
                <a
                    href="{{ route('admin.staff.attendance', [
                        'id' => $user->id,
                        'month' => \Carbon\Carbon::parse($month)->subMonth()->format('Y-m'),
                    ]) }}">
                    <span class="left-arrow">←</span>前月
                </a>

                <div class="month-nav__current">
                    <span>
                        <i class="fa-solid fa-calendar-days"></i>
                        {{ \Carbon\Carbon::parse($month)->format('Y/m') }}
                    </span>
                </div>
                <a
                    href="{{ route('admin.staff.attendance', [
                        'id' => $user->id,
                        'month' => \Carbon\Carbon::parse($month)->addMonth()->format('Y-m'),
                    ]) }}">翌月
                    <span class="right-arrow">→</span>
                </a>
            </div>

            <table class="attendance-list__table">
                <tr class="attendance-list__row">
                    <th class="attendance-list__label">
                        日付
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
                        <a href="">詳細</a>
                    </th>
                </tr>


                @foreach ($attendances as $attendance)
                    <tr class="attendance-list__row">
                        <td class="attendance-list__data">
                            {{ \Carbon\Carbon::parse($attendance->work_date)->isoFormat('MM/DD(ddd)') }}</td>
                        <td class="attendance-list__data">
                            {{ $attendance->start_time ? $attendance->start_time->format('H:i') : ' ' }}
                        </td>
                        <td class="attendance-list__data">
                            {{ $attendance->end_time ? $attendance->end_time->format('H:i') : '' }}
                        </td>
                        <td class="attendance-list__data">{{ $attendance->break_duration_format ?? '' }}</td>
                        <td class="attendance-list__data">{{ $attendance->work_duration_format ?? '' }}</td>
                        <td class="attendance-list__data">
                            <div class="disable">
                                @if ($attendance->start_time)
                                    <a href="{{ route('admin.attendance.detail', ['id' => $attendance->id]) }}">詳細</a>
                                @else
                                    <span>詳細</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </table>
            <div class="submit">
                <a href="{{ route('csv.download', ['user_id' => $user->id, 'month' => $month]) }}"
                    class="submit__btn">CSV出力</a>
            </div>
        </div>
    </div>
@endsection
