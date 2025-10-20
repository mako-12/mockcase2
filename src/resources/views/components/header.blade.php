<header class="header">
    @auth
        {{-- 一般ユーザー用ヘッダー --}}
        @if (Auth::user()->role === 0)
            @if (isset($attendance) && $attendance->status === \App\Models\Attendance::STATUS_FINISHED)
                <div class="header__logo">
                    <a href=""><img src="{{ asset('img/logo.png') }}" alt="ロゴ"></a>
                </div>
                <nav class='header__nav'>
                    <ul>
                        <li><a href="{{ route('attendance.list') }}">今月の出勤一覧</a></li>
                        <li><a href="">申請一覧<a></li>
                        <form action="/logout" method="post">
                            @csrf
                            <button class="header__logout">ログアウト</button>
                        </form>
                    </ul>
                </nav>
            @else
                <div class="header__logo">
                    <a href=""><img src="{{ asset('img/logo.png') }}" alt="ロゴ"></a>
                </div>
                <nav class='header__nav'>
                    <ul>
                        <li><a href="{{ route('general.attendance') }}">勤怠</a></li>
                        <li><a href="{{ route('attendance.list') }}">勤怠一覧</a></li>
                        <li><a href="">申請</a></li>
                        <form action="/logout" method="post">
                            @csrf
                            <button class="header__logout">ログアウト</button>
                        </form>
                    </ul>
                </nav>
            @endif
            {{-- 管理者用ヘッダー --}}
        @elseif (Auth::user()->role === 1)
            <div class="header__logo">
                <a href=""><img src="{{ asset('img/logo.png') }}" alt="ロゴ"></a>
            </div>
            <nav class="header__nav">
                <ul>
                    <li><a href="">勤怠一覧</a></li>
                    <li><a href="">スタッフ一覧</a></li>
                    <li><a href="">申請一覧</a></li>
                    <li>
                        <form action="/admin/logout" method="post">
                            @csrf
                            <button class="header__logout">ログアウト</button>
                        </form>
                    </li>
                </ul>
            </nav>
        @endif

        {{-- ログイン前 --}}
    @else
        <div class="header__logo">
            <img src="{{ asset('img/logo.png') }}" alt="ロゴ">
        </div>
    @endauth
</header>
