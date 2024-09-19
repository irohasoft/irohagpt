<?php
/**
 * iroha GPT Project
 *
 * @author        Kotaro Miura
 * @copyright     2015-2021 iroha Soft, Inc. (https://irohasoft.jp)
 * @link          https://docs.irohagpt.com
 * @license       https://www.gnu.org/licenses/gpl-3.0.en.html GPL License
 */

App::uses('AppController', 'Controller');

/**
 * Messages Controller
 * https://book.cakephp.org/2/ja/controllers.html
 */
class MessagesController extends AppController
{
	/**
	 * 使用するコンポーネント
	 * https://book.cakephp.org/2/ja/core-libraries/toc-components.html
	 */
	public $components = [
		'Security' => [
			'validatePost' => false,
			'csrfUseOnce' => false,
			//'csrfCheck' => false,
			'csrfExpires' => '+3 hours',
			'csrfLimit' => 10000,
			'unlockedActions' => ['admin_order', 'index', 'api']
		],
	];

	/**
	 * メッセージ一覧
	 * @param string $chat_key チャットキー
	 */
	public function index($chat_key)
	{
		$user_id = $this->readAuthUser('id');
		$first_message = $this->getData('message');
		$first_image_urls = $this->getData('image_urls');
		$template_id = $this->getData('template_id');

		$messages = $this->fetchTable('Message')->find()
			->where(['Message.user_id' => $user_id, 'Message.chat_key' => $chat_key])
			->order('Message.created')
			->all();
		
		$chat = $this->fetchTable('Chat')->find()
			->where(['Chat.user_id' => $user_id, 'Chat.chat_key' => $chat_key])
			->first();
		
		//debug($first_message);

		$this->set(compact('template_id', 'messages', 'chat', 'chat_key', 'first_message', 'first_image_urls'));
	}

	/**
	 * 問題一覧を表示
	 * @param string $chat_key 表示するチャットのID
	 * @param int $user_id ユーザID
	 */
	public function admin_index($chat_key, $user_id)
	{
		$messages = $this->fetchTable('Message')->find()
			->where(['Message.user_id' => $user_id, 'Message.chat_key' => $chat_key])
			->order('Message.created')
			->all();
		
		$chat = $this->fetchTable('Chat')->find()
			->where(['Chat.user_id' => $user_id, 'Chat.chat_key' => $chat_key])
			->first();
		
		$template_id = null;
		$first_message = null;
		
		$this->set(compact('template_id', 'messages', 'chat', 'chat_key', 'first_message'));
		$this->render('index');
	}
}
