@extends('layouts.default')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/request.css') }}">
@endsection

@section('content')
    @include('components.header')

    <div class="page__header">
        <h2>勤怠一覧</h2>
    </div>

    <input type="radio" id="pending-approval" name="tab__btn" checked>
    <input type="radio" id="approved" name="tab__btn">

    <div class="tab">
        <label for="pending-approval" class="tab__label">承認待ち</label>
        <label for="approved" class="tab__label">承認済み</label>
    </div>

    <div class="tab__page">
        <div class="tab__panel" id="panel-pending-approval">
            <table class="request__table">
                <tr class="request__row">
                    <td class="request__label">状態</td>
                    <td class="request__label">名前</td>
                    <td class="request__label">対象日時</td>
                    <td class="request__label">申請理由</td>
                    <td class="request__label">申請日時</td>
                    <td class="request__label">詳細</td>
                </tr>

                @foreach ($attendanceRequests as $attendanceRequest)
                    @if (isset($attendanceRequest->status) && $attendanceRequest->status == 0)
                        <tr class="request__row">
                            <td class="request__date">
                                {{-- 状態 --}}
                                {{ $attendanceRequest->status_name }}
                            </td>
                            {{-- 名前 --}}
                            <td class="request_date">
                                {{ $attendanceRequest->user->name }}
                            </td>
                            {{-- 対象日時 --}}
                            <td class="request_date">
                                {{ $attendanceRequest->attendance->work_date->format('Y/m/d') }}
                            </td>
                            {{-- 対象日時 --}}
                            <td class="request_date">
                                {{ $attendanceRequest->reason }}
                            </td>
                            {{-- 申請日時 --}}
                            <td class="request_date">
                                {{ $attendanceRequest->request_date->format('Y/m/d') }}
                            </td>
                            {{-- 詳細 --}}
                            <td class="request_date">
                                <a href="{{ route('attendance.detail', ['id' => $attendanceRequest->attendance->id]) }}"
                                    class="">詳細</a>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </table>
        </div>

        <div class="tab__panel" id="panel-approved">
            <p>承認済みページ</p>
            <table class="request__table">
                <tr class="request__row">
                    <td class="request__label">状態</td>
                    <td class="request__label">名前</td>
                    <td class="request__label">対象日時</td>
                    <td class="request__label">申請理由</td>
                    <td class="request__label">申請日時</td>
                    <td class="request__label">詳細</td>
                </tr>

                <tr class="request__row">

                </tr>
            </table>
        </div>

    </div>
@endsection
