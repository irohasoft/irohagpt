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
	 * テスト結果を表示
	 * @param int $content_id 表示するコンテンツ(テスト)のID
	 * @param int $record_id 履歴ID
	 */
	public function record($content_id, $record_id)
	{
		$this->index($content_id, $record_id);
		$this->render('index');
	}

	/**
	 * 問題を削除
	 * @param int $message_id 削除対象の問題のID
	 */
	public function delete($chat_key, $message_id = null)
	{
		$this->Message->id = $message_id;
		
		if(!$this->Message->exists())
		{
			throw new NotFoundException(__('Invalid contents message'));
		}
		
		$this->request->allowMethod('post', 'delete');
		
		// 問題情報を取得
		$message = $this->Message->get($message_id);
		
		if($this->Message->delete())
		{
			$this->Flash->success(__('履歴が削除されました'));
			return $this->redirect([
				'controller' => 'contents_messages',
				'action' => 'index',
				$chat_key,
				$message['Message']['content_id']
			]);
		}
		else
		{
			$this->Flash->error(__('The contents message could not be deleted. Please, try again.'));
		}
		return $this->redirect(['action' => 'index']);
	}

	/**
	 * テスト結果を表示
	 * @param int $content_id 表示するコンテンツ(テスト)のID
	 * @param int $record_id 履歴ID
	 */
	public function admin_record($content_id, $record_id)
	{
		$this->record($content_id, $record_id);
	}

	/**
	 * 問題一覧を表示
	 * @param int $content_id 表示するコンテンツ(テスト)のID
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

	/**
	 * 問題を追加
	 * @param int $content_id 追加対象のコンテンツ(テスト)のID
	 */
	public function admin_add($content_id)
	{
		$this->admin_edit($content_id);
		$this->render('admin_edit');
	}

	/**
	 * 問題を編集
	 * @param int $content_id 追加対象のコンテンツ(テスト)のID
	 * @param int $message_id 編集対象の問題のID
	 */
	public function admin_edit($content_id, $message_id = null)
	{
		$content_id = intval($content_id);
		
		if($this->isEditPage() && !$this->Message->exists($message_id))
		{
			throw new NotFoundException(__('Invalid contents question'));
		}

		// コンテンツ情報を取得
		$content = $this->fetchTable('Chat')->get($content_id);
		
		if($this->request->is(['post', 'put']))
		{
			if($message_id == null)
			{
				$this->request->data['Message']['user_id'] = $this->readAuthUser('id');
				$this->request->data['Message']['content_id'] = $content_id;
				$this->request->data['Message']['sort_no']   = $this->Message->getNextSortNo($content_id);
			}
			
			if(!$this->Message->validates())
				return;
			
			if($this->Message->save($this->request->data))
			{
				$this->Flash->success(__('問題が保存されました'));
				return $this->redirect([
					'controller' => 'contents_questions',
					'action' => 'index',
					$content_id
				]);
			}
			else
			{
				$this->Flash->error(__('The contents question could not be saved. Please, try again.'));
			}
		}
		else
		{
			$this->request->data = $this->Message->get($message_id);
		}
		
		$this->set(compact('content'));
	}

	/**
	 * 問題を削除
	 * @param int $message_id 削除対象の問題のID
	 */
	public function admin_delete($message_id = null)
	{
		$this->Message->id = $message_id;
		
		if(!$this->Message->exists())
		{
			throw new NotFoundException(__('Invalid contents question'));
		}
		
		$this->request->allowMethod('post', 'delete');
		
		// 問題情報を取得
		$message = $this->Message->get($message_id);
		
		if($this->Message->delete())
		{
			$this->Flash->success(__('問題が削除されました'));
			return $this->redirect([
				'controller' => 'contents_questions',
				'action' => 'index',
				$message['Message']['content_id']
			]);
		}
		else
		{
			$this->Flash->error(__('The contents question could not be deleted. Please, try again.'));
		}
		return $this->redirect(['action' => 'index']);
	}

	/**
	 * Ajax によるコンテンツの並び替え
	 *
	 * @return string 実行結果
	 */
	public function admin_order()
	{
		$this->autoRender = FALSE;
		
		if($this->request->is('ajax'))
		{
			$this->Message->setOrder($this->data['id_list']);
			return 'OK';
		}
	}
}
