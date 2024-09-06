<?php
/**
 * iroha GPT Project
 *
 * @author        Kotaro Miura
 * @copyright     2015-2021 iroha Soft, Inc. (https://irohasoft.jp)
 * @link          https://docs.irohagpt.com/
 * @license       https://www.gnu.org/licenses/gpl-3.0.en.html GPL License
 */

$config['group_status']		= ['1' => '公開', '0' => '非公開'];
$config['template_status']	= ['1' => '有効', '0' => '無効'];

$config['user_role'] = ['admin' => '管理者', 'user' => '利用者'];

// select2 項目選択時の自動クローズの設定 (true ; 自動的にメニューを閉じる, false : 閉じない)
$config['close_on_select'] = true;

// デモモード (true ; 設定する, false : 設定しない)
$config['demo_mode'] = false;

// デモユーザのログインIDとパスワード
$config['demo_login_id'] = "demo001";
$config['demo_password'] = "pass";

// フォームのスタイル(BoostCake)の基本設定
$config['form_defaults'] = [
	'inputDefaults' => [
		'div' => 'form-group',
		'label' => [
			'class' => 'col col-sm-3 control-label'
		],
		'wrapInput' => 'col col-sm-9',
		'class' => 'form-control'
	],
	'class' => 'form-horizontal'
];

$config['form_submit_defaults'] = [
	'div' => false,
	'class' => 'btn btn-primary'
];

$config['form_submit_before'] = 
	 '<div class="form-group">'
	.'  <div class="col col-sm-9 col-sm-offset-3">';

$config['form_submit_after'] = 
	 '  </div>'
	.'</div>';

$config['theme_colors'] = [
	'#31708f' => 'default',
	'#003f8e' => 'ink blue',
	'#4169e1' => 'royal blue',
	'#006888' => 'marine blue',
	'#00bfff' => 'deep sky blue',
	'#483d8b' => 'dark slate blue',
	'#00a960' => 'green',
	'#006948' => 'holly green',
	'#288c66' => 'forest green',
	'#556b2f' => 'dark olive green',
	'#8b0000' => 'dark red',
	'#d84450' => 'poppy red',
	'#c71585' => 'medium violet red',
	'#a52a2a' => 'brown',
	'#ee7800' => 'orange',
	'#fcc800' => 'chrome yellow',
	'#7d7d7d' => 'gray',
	'#696969' => 'dim gray',
	'#2f4f4f' => 'dark slate gray',
	'#000000' => 'black'
];

$config['import_group_count']  = 10;

$config['show_admin_link'] = false;
$config['open_link_same_window'] = false;

// webroot/index.php でアプリケーション名が設定されていない場合、ここで設定
if (!defined('APP_NAME')) {
	define('APP_NAME', 'iroha Chat');
}

$config['prompt_max']  = 4000;

$config['upload_image_extensions'] = [
	'.png',
	'.gif',
	'.jpg',
	'.jpeg',
];

$config['upload_image_maxsize'] = 1024 * 1024 *  2;
