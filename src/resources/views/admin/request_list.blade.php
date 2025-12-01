@extends('layouts.default')

@section('title', '申請一覧画面')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/request.css') }}">
@endsection

@section('content')
    @include('components.header')
    <div class="content">
        <div class="inner">
            <div class="page__header">
                <h2>申請一覧</h2>
            </div>
            <input type="radio" class="request-list" name="tab__btn" id="pending-approval" checked>
            <input type="radio" class="request-list" name="tab__btn" id="approved">
            <div class="tab">
                <label for="pending-approval" class="tab__label">承認待ち</label>
                <label for="approved" class="tab__label">承認済み</label>
            </div>
            <div class="tab__page">
                <div class="tab__panel" id="panel-pending-approval">
                    <table class="request__table">
                        <tr class="request__row">
                            <th class="request__label">状態</th>
                            <th class="request__label">名前</th>
                            <th class="request__label">対象日時</th>
                            <th class="request__label">申請理由</th>
                            <th class="request__label">申請日時</th>
                            <th class="request__label">
                                <a href="">詳細</a>
                            </th>
                        </tr>

                        @foreach ($attendanceRequests as $attendanceRequest)
                            @if (isset($attendanceRequest->status) && $attendanceRequest->status == 0)
                                <tr class="request__row">
                                    <td class="request__data">{{ $attendanceRequest->status_name }}</td>
                                    <td class="request__data">{{ $attendanceRequest->user->name }}</td>
                                    <td class="request__data">
                                        {{ $attendanceRequest->attendance->work_date->format('Y/m/d') }}</td>
                                    <td class="request__data">{{ $attendanceRequest->reason }}</td>
                                    <td class="request__data">{{ $attendanceRequest->request_date->format('Y/m/d') }}</td>
                                    <td class="request__data">
                                        <div class="disable">
                                            <a
                                                href="{{ route('admin.request.approval', ['attendance_correct_request_id' => $attendanceRequest->id]) }}">詳細</a>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </table>
                </div>
                <div class="tab__panel" id="panel-approved">
                    <table class="request__table">
                        <tr class="request__row">
                            <th class="request__label">状態</th>
                            <th class="request__label">名前</th>
                            <th class="request__label">対象日時</th>
                            <th class="request__label">申請理由</th>
                            <th class="request__label">申請日時</th>
                            <th class="request__label">詳細</th>
                        </tr>
                        @foreach ($attendanceRequests as $attendanceRequest)
                            @if (isset($attendanceRequest->status) && $attendanceRequest->status == 1)
                                <tr class="request__row">
                                    <td class="request__data">{{ $attendanceRequest->status_name }}</td>
                                    <td class="request__data">{{ $attendanceRequest->user->name }}</td>
                                    <td class="request__data">
                                        {{ $attendanceRequest->attendance->work_date->format('Y/m/d') }}</td>
                                    <td class="request__data">{{ $attendanceRequest->reason }}</td>
                                    <td class="request__data">{{ $attendanceRequest->request_date->format('Y/m/d') }}</td>
                                    <td class="request__data">
                                        <div class="disable">
                                            <a
                                                href="{{ route('admin.request.approval', ['attendance_correct_request_id' => $attendanceRequest->id]) }}">詳細</a>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
