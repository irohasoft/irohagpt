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

/**
 * Chat Model
 *
 * @property Group $Group
 * @property Template $Template
 * @property User $User
 * @property Message $Message
 */
class Chat extends AppModel
{
	/**
	 * バリデーションルール
	 * https://book.cakephp.org/2/ja/models/data-validation.html
	 * @var array
	 */
	public $validate = [
		'template_id'  => ['numeric' => ['rule' => ['numeric']]],
		'user_id'    => ['numeric' => ['rule' => ['numeric']]],
	];

	/**
	 * アソシエーションの設定
	 * https://book.cakephp.org/2/ja/models/associations-linking-models-together.html
	 * @var array
	 */
	public $hasMany = [
		'Message' => [
			'className' => 'Message',
			'foreignKey' => 'chat_key',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => 'Message.id',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		]
	];

	public $belongsTo = [
		/*
		'Template' => [
			'className' => 'Template',
			'foreignKey' => 'template_id',
			'type'=>'inner',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		],
		*/
		'User' => [
			'className' => 'User',
			'foreignKey' => 'user_id',
			'type'=>'inner',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		],
	];
	
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
		'template_id' => [
			'type' => 'value',
			'field' => 'template_id'
		],
		'message_title' => [
			'type' => 'like',
			'field' => 'Message.title'
		],
		'username' => [
			'type' => 'like',
			'field' => 'User.username'
		],
		'name' => [
			'type' => 'like',
			'field' => 'User.name'
		],
	];
}
