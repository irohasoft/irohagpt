<?php
/**
 * iroha GPT Project
 *
 * @author        Kotaro Miura
 * @copyright     2015-2021 iroha Soft, Inc. (https://irohasoft.jp)
 * @link          https://docs.irohagpt.com/
 * @license       https://www.gnu.org/licenses/gpl-3.0.en.html GPL License
 */

App::uses('AppModel', 'Model');
App::uses('BlowfishPasswordHasher', 'Controller/Component/Auth');

/**
 * User Model
 *
 * @property Group $Group
 * @property Chat $Chat
 * @property Template $Template
 */
class User extends AppModel
{
	public $order = "User.name"; // デフォルトのソート条件

	/**
	 * バリデーションルール
	 * https://book.cakephp.org/2/ja/models/data-validation.html
	 * @var array
	 */
	public $validate = [
		'username' => [
			[
				'rule' => 'isUnique',
				'message' => 'ログインIDが重複しています'
			],
			[
				'rule' => 'alphaNumericMB',
				'message' => 'ログインIDは英数字で入力して下さい'
			],
			[
				'rule' => ['between', 4, 32],
				'message' => 'ログインIDは4文字以上32文字以内で入力して下さい'
			]
		],
		'name' => [
			'notBlank' => [
				'rule' => ['notBlank'],
				'message' => '氏名が入力されていません'
			]
		],
		'role' => [
			'notBlank' => [
				'rule' => ['notBlank'],
				'message' => '権限が指定されていません'
			]
		],
		'password' => [
			[
				'rule' => 'alphaNumericMB',
				'message' => 'パスワードは英数字で入力して下さい'
			],
			[
				'rule' => ['between', 4, 32],
				'message' => 'パスワードは4文字以上32文字以内で入力して下さい'
			]
		],
		'new_password' => [
			[
				'rule' => 'alphaNumericMB',
				'message' => 'パスワードは英数字で入力して下さい',
				'allowEmpty' => true
			],
			[
				'rule' => ['between', 4, 32],
				'message' => 'パスワードは4文字以上32文字以内で入力して下さい',
				'allowEmpty' => true
			]
		]
	];

	/**
	 * アソシエーションの設定
	 * https://book.cakephp.org/2/ja/models/associations-linking-models-together.html
	 * @var array
	 */
	public $hasAndBelongsToMany = [
		'Group' => [
			'className' => 'Group',
			'joinTable' => 'users_groups',
			'foreignKey' => 'user_id',
			'associationForeignKey' => 'group_id',
			'unique' => 'keepExisting',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => ''
	 	]
	];

	public function beforeSave($options = [])
	{
		// ユーザ情報保存時に、パスワードをハッシュ値に変換
		if (isset($this->data[$this->alias]['password']))
		{
			$this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
		}
		return true;
	}
	
	// custom
	public function isEmailExist($email)
	{
		$data = $this->findByEmail($email);
		
		return (count($data) > 0);
	}

	/**
	 * 検索用
	 */
	public $actsAs = [
		'Search.Searchable'
	];

	/**
	 * 検索条件
	 * https://github.com/CakeDC/search/blob/master/Docs/Home.md
	 */
	public $filterArgs = [
		'username' => [
			'type' => 'like',
			'field' => 'User.username'
		],
		'name' => [
			'type' => 'like',
			'field' => 'User.name'
		],
	];

	/**
	 * チャット履歴の削除
	 * 
	 * @param int array $user_id チャット履歴を削除するユーザのID
	 */
	public function deleteUserChats($user_id)
	{
		// メッセージレコードを先に削除
		$sql = 'DELETE FROM ib_messages WHERE user_id = :user_id';
		
		$params = [
			'user_id' => $user_id,
		];
		
		$this->query($sql, $params);
		
		// チャット履歴レコードを削除
		$sql = 'DELETE FROM ib_chats WHERE user_id = :user_id';
		$this->query($sql, $params);
	}
}
