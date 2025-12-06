@extends('layouts.default')

@section('title', '申請一覧画面')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/detail.css') }}">
    <link rel="stylesheet" href="{{ asset('css/approval.css') }}">
@endsection

@section('content')

    @php use App\Models\AttendanceRequest; @endphp

    @include('components.header')
    <div class="content">
        <div class="inner">
            <div class="page__header">
                <h2>勤怠詳細</h2>
            </div>

            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach


            <form action="{{ route('admin.approve.request', $attendanceRequest->id) }}" method="post">
                @csrf
                <table class="detail__table">
                    <tr class="detail__row">
                        <th class="detail__label">名前</th>
                        <td class="detail__data">{{ $attendanceRequest->user->name }}</td>
                    </tr>

                    <tr class="detail__row">
                        <th class="detail__label">日付</th>
                        <td class="detail__data">{{ $attendanceRequest->attendance->work_date->format('Y年') }}
                            {{ $attendanceRequest->attendance->work_date->format('n月j日') }}
                        </td>
                    </tr>

                    <tr class="detail__row">
                        <th class="detail__label">出勤・退勤</th>
                        <td class="detail__data">
                            <input type="time" name="requested_start_time"
                                value="{{ $attendanceRequest->requested_start_time ? $attendanceRequest->requested_start_time->format('H:i') : '' }}"
                                disabled>
                            <span class="separator">～</span>

                            <input type="time" name="requested_end_time"
                                value="{{ $attendanceRequest->requested_end_time ? $attendanceRequest->requested_end_time->format('H:i') : '' }}"
                                disabled>

                        </td>
                    </tr>
                    @foreach ($attendanceRequest->breakRequests as $index => $break)
                        <tr class="detail__row">
                            <th class="detail__label">
                                {{ $index === 0 ? '休憩' : '休憩' . $index + 1 }}
                            </th>
                            <td class="detail__data">
                                <input type="time" name="requested_break_start[]"
                                    value="{{ $break->requested_break_start ? $break->requested_break_start->format('H:i') : '' }}"disabled>
                                <span class="separator">～</span>
                                <input type="time" name="requested_break_end[]"
                                    value="{{ $break->requested_break_end ? $break->requested_break_end->format('H:i') : '' }}"disabled>
                            </td>
                        </tr>
                    @endforeach

                    <tr class="detail__row">
                        <th class="detail__label">備考</th>
                        <td class="detail__data">
                            <input type="text" name="reason" value="{{ $attendanceRequest->reason }}" disabled>
                        </td>
                    </tr>
                </table>


                <div class="submit">
                    @if ($attendanceRequest->status == AttendanceRequest::STATUS_PENDING)
                        <button class="detail__submit-btn--approval">承認</button>
                    @else
                        <button class="detail__submit-btn--approve" disabled>承認済み</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
@endsection
