<header class="header">
    @guest
        <div class="header__logo">
            <img src="{{ asset('img/logo.png') }}" alt="ロゴ">
        </div>
    @endguest

    @auth
        {{-- 一般ユーザー用ヘッダー --}}
        @if (Auth::user()->role === 0)
            @if (isset($attendance) &&
                    $attendance->status === \App\Models\Attendance::STATUS_FINISHED &&
                    Auth::user()->hasVerifiedEmail())
                <div class="header__logo">
                    <a href="{{ route('general.attendance') }}"><img src="{{ asset('img/logo.png') }}" alt="ロゴ"></a>
                </div>
                <nav class='header__nav'>
                    <ul>
                        <li><a href="{{ route('attendance.list') }}">今月の出勤一覧</a></li>
                        <li><a href="{{ route('general.attendance.request') }}">申請一覧</a></li>
                        <li>
                            <form action="/logout" method="post">
                                @csrf
                                <button class="header__logout">ログアウト</button>
                            </form>
                        </li>
                    </ul>
                </nav>
            @else
                <div class="header__logo">
                    <a href="{{ route('general.attendance') }}">
                        <img src="{{ asset('img/logo.png') }}" alt="ロゴ"></a>
                </div>
                <nav class='header__nav'>
                    <ul>
                        <li><a href="{{ route('general.attendance') }}">勤怠</a></li>
                        <li><a href="{{ route('attendance.list') }}">勤怠一覧</a></li>
                        <li><a href="{{ route('general.attendance.request') }}">申請</a></li>
                        <li>
                            <form action="/logout" method="post"> @csrf <button class="header__logout">ログアウト</button>
                            </form>
                        </li>
                    </ul>
                </nav>
            @endif
            {{-- 管理者用ヘッダー --}}
        @elseif (Auth::user()->role === 1)
            <div class="header__logo">
                <a href="{{ route('admin.attendance.list') }}"><img src="{{ asset('img/logo.png') }}" alt="ロゴ"></a>
            </div>
            <nav class="header__nav">
                <ul>
                    <li><a href="{{ route('admin.attendance.list') }}">勤怠一覧</a></li>
                    <li><a href="{{ route('admin.staff.list') }}">スタッフ一覧</a></li>
                    <li><a href="{{ route('admin.attendance.request') }}">申請一覧</a></li>
                    <li>
                        <form action="/admin/logout" method="post"> @csrf <button class="header__logout">ログアウト</button>
                        </form>
                    </li>
                </ul>
            </nav>
        @endif
    @endauth
</header>
