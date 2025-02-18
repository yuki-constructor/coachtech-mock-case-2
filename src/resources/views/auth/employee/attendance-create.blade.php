<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>メール認証誘導</title>
    <link rel="stylesheet" href="{{asset('css/auth/employee/attendance-create.css')}}" />
  </head>
  <body>
    <header class="header">
      <div class="header-container">
        <div class="header-left">
          <img src="{{ asset("storage/photos/logo_images/logo.svg") }}" alt="COACHTECH ロゴ" class="logo" />
        </div>
        <div class="header-center"></div>
        <div class="header-right">
          <nav class="nav">
            <ul class="nav__ul">
              <li>
                <form action="{{ route('logout') }}" method="POST">
                  <!-- @csrf -->
                  <button type="submit" class="nav__attendance">勤怠</button>
                </form>
              </li>
              <li>
                <form action="" method="GET">
                  <!-- @csrf -->
                  <button type="submit" class="nav__attendance-list">
                    勤怠一覧
                  </button>
                </form>
              </li>
              <li>
                <form action="" method="GET">
                  <!-- @csrf -->
                  <button type="submit" class="nav__application">申請</button>
                </form>
              </li>
              <li>
                <form action="{{ route('logout') }}" method="POST">
                  <!-- @csrf -->
                  <button type="submit" class="nav__logout">ログアウト</button>
                </form>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </header>

    <main>
      <div class="container-wrap">
        <div class="container">
          <div class="form-group">
            <form class="form">
              <p class="work-status">勤務外</p>
              <p class="date">2023年6月1日(木)</p>
              <p class="time">08:00</p>
              <button type="submit" class="form-group__submit-btn">出勤</button>
            </form>
          </div>
        </div>
      </div>
    </main>
  </body>
</html>
