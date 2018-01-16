# StampCast

## 環境
- Laravel 5.5 (PHP 7以上)
- MySQL or MariaDB
- Redis
- NodeJS

## 開発メモ

1. gitからcloneしてローカルにソースをダウンロード
1. Homestead環境下で使えるように各種セットアップ
1. HomesteadにSSH接続して以下作業
1. cp .env.dev .env
1. 必要に応じて.envの内容を修正
1. cp laravel-echo-server.json.dev laravel-echo-server.json
1. 必要に応じてlaravel-echo-server.jsonの内容を修正
1. chmod -R 777 storage/
1. chmod -R 777 bootstrap/cache/
1. composer install
1. npm install
1. php artisan storage:link
1. php artisan migrate:refresh --seed
1. laravel-echo-server start
1. ブラウザで http://stamp.test にアクセス
