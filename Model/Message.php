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
 * Message Model
 *
 * @property Chat $Chat
 * @property Message $Message
 */
class Message extends AppModel
{
	public $order = "Message.id";  

	/**
	 * バリデーションルール
	 * https://book.cakephp.org/2/ja/models/data-validation.html
	 * @var array
	 */
	public $validate = [
		'record_id'   => ['numeric' => ['rule' => ['numeric']]],
		'question_id' => ['numeric' => ['rule' => ['numeric']]],
		'score'       => ['numeric' => ['rule' => ['numeric']]]
	];
	
	/**
	 * アソシエーションの設定
	 * https://book.cakephp.org/2/ja/models/associations-linking-models-together.html
	 * @var array
	 */
	public $belongsTo = [
		'Chat' => [
			'className' => 'Chat',
			'foreignKey' => 'chat_key',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		],
	];
}
