<header class="header">
    {{-- ログイン・登録・メール認証画面 --}}
    @if (in_array(Route::currentRouteName(), ['register', 'login', 'verification.notice', 'admin.login']))
        <div class="header__logo">
            <img src="{{ asset('img/logo.png') }}" alt="ロゴ">
        </div>

        {{-- 管理者用ヘッダー --}}
    @elseif(Str::startsWith(Route::currentRouteName(), 'admin'))
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

        {{-- 出勤登録画面（退勤済）の画面用 --}}
    @elseif(Route::currentRouteName() === 'general.attendance' &&
            !empty($attendance) &&
            $attendance->status === \App\Models\Attendance::STATUS_FINISHED)
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
        {{-- 一般ユーザー用ヘッダー --}}
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

</header>
