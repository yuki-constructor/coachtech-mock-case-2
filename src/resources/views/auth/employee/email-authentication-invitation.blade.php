<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>メール認証誘導</title>
    <link rel="stylesheet" href="{{ asset('css/auth/employee/email-authentication-invitation.css') }}" />
</head>

<body>
    <header class="header">
        <div class="header-container">
            <div class="header-left">
                <img src="{{ asset('storage/photos/logo_images/logo.svg') }}" alt="COACHTECH ロゴ" class="logo" />
            </div>
            <div class="header-center"></div>
            <div class="header-right"></div>
        </div>
    </header>

    <main>
        <div class="container-wrap">
            <div class="container">
                <p class="message">登録していただいたメールアドレスに認証メールを送付しました。</p>
                <p class="message">メール認証を完了してください。</p>
                <div class="mail-check-link">
                    <a class="mail-check-link__btn" href="http://localhost:8025">認証はこちらから</a>
                </div>
                <div class="send-mail-link">
                    <form action="{{ route('verification.resend', ['employeeId' => $employee->id]) }}" method="POST">
                        @csrf
                        <button type="submit" class="send-mail-link__btn">
                            認証メールを送信する
                        </button>
                    </form>
                </div>
                </p>
            </div>
        </div>
    </main>
</body>

</html>
