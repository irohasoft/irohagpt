<?php
class DATABASE_CONFIG
{
	// iroha GPT で使用するデータベース
	public $default = [
		'datasource' => 'Database/Mysql', // 変更しないでください
		'persistent' => true,
		'host' => 'localhost', // MySQLサーバのホスト名
		'login' => 'root', // ユーザ名
		'password' => '', // パスワード
		'database' => 'irohagpt', // データベース名
		'prefix' => 'ib_', // 変更しないでください
		'encoding' => 'utf8'
	];
}
