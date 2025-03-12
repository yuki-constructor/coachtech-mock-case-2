# アプリケーション名

coachtech 勤怠管理アプリ

# 環境構築

## ⓵ リポジトリをクローン

以下のコマンドで、Git リポジトリをクローンします。

$ git clone git@github.com:yuki-constructor/coachtech-mock-case-2.git

## ⓶.env ファイルの作成

 src ディレクトリに移動し、.env.example を .env にコピーします。

$ cd coachtech-mock-case2/src/

$ cp .env.example .env


.env ファイルを開いて、以下の設定を変更します。


APP_TIMEZONE=Asia/Tokyo

APP_LOCALE=ja


DB_CONNECTION=mysql

DB_HOST=mysql

DB_PORT=3306

DDB_DATABASE=laravel_db

DB_USERNAME=laravel_user

DB_PASSWORD=laravel_pass

## ⓷Docker コンテナのビルドと起動

以下のコマンドで、Docker コンテナを起動します。

$ docker-compose up --build -d

## ⓸PHP コンテナ内にログイン

以下のコマンドで、PHP コンテナに接続します。

$ docker-compose exec php bash

## ⓹composer のインストール

以下のコマンドで、composer をインストールします。

$ composer install

## ⓺ アプリケーションキーの生成

以下のコマンドで、Laravel のアプリケーションキーを生成します。

$ php artisan key:generate

## ⓻ シンボリックリンクを設定

以下のコマンドで、画像を公開ディレクトリからアクセス可能にするために、シンボリックリンクを設定します。

$ php artisan storage:link

## ⓼ データベースのマイグレーション

以下のコマンドで、データベースをセットアップするために、マイグレーションを実行します。

$ php artisan migrate

  INFO  Nothing to migrate.と表示された場合も、次に進んでください。

## ⑨phpMyAdmin の動作確認

<http://localhost:8080> にアクセスすることで、phpMyAdmin を確認できます。

## ⓾ データベースのシーディング

以下のコマンドで、データベースにサンプルデータを挿入するためにシーディングを実行します。

$ php artisan migrate:fresh --seed

## ⑪ アプリケーションの動作確認

<http://localhost/admin/login>か、<http://localhost/employee/login> にアクセスすることで、アプリケーションが動作していることを確認できます。

もし、エラーとなった場合、ルートディレクトリで以下のコマンドを実行し、ディレクトリ書き込み権限を設定することで改善するか確認してください。

$ sudo chmod -R 777 src/*

（PHP コンテナ内に入っている場合は、以下を実行）
$ chmod -R 777 www/.*

## ⑫Mailhog の設定

### ⑫-1. 　 .envファイルにて Mailhog の設定

以下を修正してください。


MAIL_MAILER=smtp 

MAIL_HOST=mailhog 

MAIL_PORT=1025 

MAIL_USERNAME=null 

MAIL_PASSWORD=null 

MAIL_ENCRYPTION=null 

MAIL_FROM_ADDRESS=hello@example.com 

MAIL_FROM_NAME="${APP_NAME}" 


以下のコマンドを実行  
$ php artisan config:clear   
$ php artisan cache:clear

### ⑫-2. 　 Mailhog の動作確認

<http://localhost:8025> にアクセスすることで、Mailhog を確認できます。


## ⑬ Fortify の設定

###⑬-1. 　 Fortify パッケージのインストール

以下のコマンドで、 Fortify 用のパッケージをインストールします。

$composer require laravel/fortify

### ⑬-2. 　 Fortify の設定ファイルの公開

以下のコマンドで、設定ファイルとリソースを公開します。

$ php artisan fortify:install

これで、config/fortify.php という設定ファイルが作成されます。

### ⑭-3. 　 Fortify のサービスプロバイダの登録について

php artisan fortify:installにより、Fortify のアクションをディレクトリに公開します（app/Actions）。また、FortifyServiceProvider構成ファイル、および必要なすべてのデータベース移行が公開されます。そのため、Laravel\Fortify\FortifyServiceProvider::class, 　　が必要なく、config/app.phpの、providers配列に以下を追加する必要がありません。 

 Laravel\Fortify\FortifyServiceProvider::class, 


# アプリケーション使用時の注意点

## ログインの試行について

### 従業員ユーザーのログインについて

#### 従業員ユーザーのログイン画面のURL
<http://localhost/employee/login>です。

#### 従業員ユーザーのログイン情報

メールアドレス：phpMyAdmin で確認できます。<http://localhost:8080> にアクセスし、employeesテーブルの”mail”カラムで確認してください。

パスワード：すべてのユーザーのパスワードは「123456789」で統一しています。

### 管理者ユーザーのログインについて

#### 管理者ユーザーのログイン画面のURL
<http://localhost/admin/login>です。

#### 管理者ユーザーのログイン情報

メールアドレス：admin@example.com

パスワード：123456789

### メール認証について

従業員ユーザーのユーザー登録時に認証メールが届きます。
<http://localhost:8025> にアクセスし、Mailhog で認証メールを確認できます。
届いた認証メール本文の認証リンクをクリックすると、会員登録が完了します。
メール認証が完了していないと、ログインができません。
（シーディングによって作成されたサンプルユーザーについては、メール認証完了済みです。）

-------------------------------------------------------


# 使用技術(実行環境)

Laravel Framework 11.3.2  
PHP 8.2 以上  
Mailhog 
Fortify  

# ER 図

![er](https://github.com/user-attachments/assets/009977fe-5a26-4a31-bc4e-ca9085eac7a5)

# URL

開発環境：
gitHub：<https://github.com/yuki-constructor/coachtech-mock-case-2.git>  
ログイン画面（管理者）：<http://localhost/admin/login>  
ログイン画面（従業員）：<http://localhost/employee/login>  
Mailhog ：<http://localhost:8025>
