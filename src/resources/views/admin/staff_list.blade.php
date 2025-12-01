@extends('layouts.default')

@section('title', 'スタッフ一覧')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/staff_list.css') }}">
@endsection

@section('content')
    @include('components.header')
    <div class="content">
        <div class="inner">
            <div class="page__header">
                <h2>スタッフ一覧</h2>
            </div>

            <table class="staff-list__table">
                <tr class="staff-list__row">
                    <th class="staff-list__label">名前</th>
                    <th class="staff-list__label">メールアドレス</th>
                    <th class="staff-list__label">月次勤怠</th>
                </tr>

                @foreach ($users as $user)
                    <tr class="staff-list__row">
                        <td class="staff-list__data">{{ $user->name }}</td>
                        <td class="staff-list__data">{{ $user->email }}</td>
                        <td class="staff-list__data">
                            <div class="disable">
                                <a href="{{ route('admin.staff.attendance', ['id' => $user->id]) }}">詳細</a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>

@endsection
