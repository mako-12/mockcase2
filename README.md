# アプリケーション名
coachtech 勤怠管理アプリ

# 環境構築
### Dockerビルド
1.git clone git@github.com:mako-12/mockcase2.git
2.DockerDesktopアプリを立ち上げる
3.docker-compose up -d --build

### Laravel環境構築
1. docker-compose exec php bash
2. composer install
3. cp .env.example .env
 - 「.env.example」ファイルを 「.env」ファイルに命名を変更。または、新しく.envファイルを作成
4. .envに以下の環境変数を追加
 ```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```
5. アプリケーションキーの作成
  php artisan key:generate
6. マイグレーションの実行
  php artisan migrate
7. シーディングの実行
  php artisan db:seed


## テーブル使用
### usersテーブル
| カラム名 | 型 | primary key | unique key | not null | foreign key |
| --- | --- | --- | --- | --- | --- |
| id | bigint | ◯ |  | ◯ |  |
| name | varchar(255) |  |  |  |  |
| email | varchar(255) |  | ◯ | ◯ |  |
| email_verified_at | timestamp |  |  |  |  |
| password | varchar(255) |  |  | ◯ |  |
| role | tinyInteger |  |  | ◯ |  |
| remember_token | varchar(100) |  |  |  |  |
| created_at | timestamp |  |  |  |  |
| updated_at | timestamp |  |  |  |  |

### attendancesテーブル
| カラム名 | 型 | primary key | unique key | not null | foreign key |
| --- | --- | --- | --- | --- | --- |
| id | bigint | ◯ |  | ◯ |  |
| user_id | bigint |  |  | ◯ | users(id) |
| work_date | date |  |  | ◯ |  |
| start_time | datetime |  |  |  |  |
| end_time | datetime |  |  |  |  |
| total_work_time | int |  |  |  |  |
| admin_note | varchar(255) |  |  |  |  |
| status | tinyint |  |  | ◯ |  |
| created_at | timestamp |  |  |  |  |
| updated_at | timestamp |  |  |  |  |

### break_timesテーブル
| カラム名 | 型 | primary key | unique key | not null | foreign key |
| --- | --- | --- | --- | --- | --- |
| id | bigint | ◯ |  | ◯ |  |
| attendance_id | bigint |  |  | ◯ | attendances(id) |
| break_start | datetime |  |  |  |  |
| break_end | datetime |  |  |  |  |
| created_at | timestamp |  |  |  |  |
| updated_at | timestamp |  |  |  |  |

### attendance_requestsテーブル
| カラム名 | 型 | primary key | unique key | not null | foreign key |
| --- | --- | --- | --- | --- | --- |
| id | bigint | ◯ |  | ◯ |  |
| user_id | bigint |  |  | ◯ | user(id) |
| attendance_id | bigint |  |  |  | attendance(id) |
| request_date | date |  |  | ◯ |  |
| requested_start_time | datetime |  |  |  |  |
| request_end_time | datetime |  |  |  |  |
| reason | string |  |  | ◯ |  |
| status | tinyint |  |  | ◯ |  |
| created_at | timestamp |  |  |  |  |
| updated_at | timestamp |  |  |  |  |

### break_requestsテーブル
| カラム名 | 型 | primary key | unique key | not null | foreign key |
| --- | --- | --- | --- | --- | --- |
| id | bigint | ◯ |  | ◯ |  |
| attendance_request_id | bigint |  |  | ◯ | attendance_requests(id) 
| break_time_id | bigint |  |  | ◯ | break_times(id) |
| requested_break_start | bigint |  |  | ◯ |  |
| requested_break_end | datetime |  |  |  |  |
| created_at | timestamp |  |  |  |  |
| updated_at | timestamp |  |  |  |  |



## ER図
<img width="782" height="552" alt="模擬案件２ER図 (3)" src="https://github.com/user-attachments/assets/1f31fb2d-3eb3-496b-aabe-32853e86924a" />

## テストアカウント
name: 管理者
email: admin@example.com
password: password
-------------------------
name: 田中　一郎
email: itiro@example.com
password: password
-------------------------
name: 佐藤　一花
email: ichika@example.com
password: password
-------------------------
name: 木村　一颯
email:issa@example.com
password: password
-------------------------

## PHPUnitを利用したテストに関して
```
//テスト用データベースの作成
docker-compose exec mysql bash
mysql -u root -p
//パスワードはrootと入力
create database test_database;

docker-compose exec php bash
cp .env .env.testing
下記の部分を変更
-------------------
APP_ENV=test
APP_KEY=
-------------------

php artisan key:generate --env=testing
php artisan migrate --env=testing
```

## 使用技術(実行環境)
- PHP 8.1
- Laravel 8.75
- MySQL 8.0.26
- nginx 1.21.1
- Docker 28.3.2
- docker-compose
- MailHog


## URL
- 開発環境：http://localhost/
- phpMyAdmin:：http://localhost:8080/
  
## 補足
-管理者ログイン画面のバリデーションについて
     管理者画面で、既存の一般ユーザーのアカウントでログインをしようとした場合、
     「管理者専用アカウントではありません。」とバリデーションメッセージが表示されるように
     実装しております。