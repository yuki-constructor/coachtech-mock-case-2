<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ログイン</title>
    <link rel="stylesheet" href="{{asset("css/auth/employee/login.css")}}"/>
  </head>
  <body>
    <header class="header">
      <div class="header-container">
        <div class="header-left">
          <img src="{{ asset("storage/photos/logo_images/logo.svg") }}" alt="COACHTECH ロゴ" class="logo" />
        </div>
        <div class="header-center">

        </div>
        <div class="header-right">

        </div>
      </div>
    </header>

    <main>
      <div class="container-wrap">
      <div class="container">

          <h1 class="title">ログイン</h1>
         <form class="form">
          <div class="form-group">
            <label class="form-group__label" for="username">メールアドレス</label>
            <input
              class="form-group__input"
              type="text"
              id="username"
              name="username"
              required
            />
          </div>
          <!-- <div class="form-group">
            <label class="form-group__label" for="email">メールアドレス</label>
            <input
              class="form-group__input"
              type="email"
              id="email"
              name="email"
              required
            />
          </div> -->
          <div class="form-group">
            <label class="form-group__label" for="password">パスワード</label>
            <input
              class="form-group__input"
              type="password"
              id="password"
              name="password"
              required
            />
          </div>
          <!-- <div class="form-group">
            <label class="form-group__label" for="confirm-password"
              >確認用パスワード</label
            >
            <input
              class="form-group__input"
              type="password"
              id="confirm-password"
              name="confirm-password"
              required
            />
          </div> -->
          <button type="submit" class="form-group__submit-btn">ログインする</button>
        </form>
        <p class="login-link">
          <a class="login-link__link-btn" href="{{ route("employee.register") }}">会員登録はこちら</a>
        </p>
      </div>
      </div>
    </main>
  </body>
</html>
