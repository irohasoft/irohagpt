<?php
/**
 * iroha GPT Project
 *
 * @author        Kotaro Miura
 * @copyright     2015-2021 iroha Soft, Inc. (https://irohasoft.jp)
 * @link          https://docs.irohagpt.com
 * @license       https://www.gnu.org/licenses/gpl-3.0.en.html GPL License
 */

App::uses('AppController',		'Controller');

/**
 * Chats Controller
 * https://book.cakephp.org/2/ja/controllers.html
 */
class ChatsController extends AppController
{
	/**
	 * 使用するコンポーネント
	 * https://book.cakephp.org/2/ja/core-libraries/toc-components.html
	 */
	public $components = [
		'Paginator',
		'Search.Prg'
	];

	/**
	 * ChatGPTのAPIから回答を取得
	 *
	 * @return string 実行結果
	 */
	public function api()
	{
		$this->autoRender = FALSE; // 自動レンダリングを無効化

		// AJAXリクエストかどうかを確認
		if(!$this->request->is('ajax'))
		{
			return false; // AJAXリクエストでない場合はfalseを返す
		}
		
		$user_id = $this->readAuthUser('id'); // 認証されたユーザーIDを取得
		
		// POSTデータから必要な情報を取得
		$chat_key = $_POST['chat_key'];
		$template_id = $_POST['template_id'];
		$messages = json_decode($_POST['messages'], true);
		$last_message = end($messages); // 最後のメッセージを取得

		// APIリクエスト用のヘッダーを設定
		$header = [
			'Authorization: Bearer ' . Configure::read('api_key'),
			'Content-type: application/json',
		];
		
		// APIリクエストのパラメータを設定
		$params = json_encode([
			'model' => Configure::read('model'),
			'messages' => $messages,
			'temperature' => floatval(Configure::read('temperature')),
			'max_tokens' => floatval(Configure::read('max_tokens')),
			'top_p' => floatval(Configure::read('top_p')),
			'frequency_penalty' => floatval(Configure::read('frequency_penalty')),
			'presence_penalty' => floatval(Configure::read('presence_penalty'))
		]);

		// cURLセッションを初期化
		$curl = curl_init('https://api.openai.com/v1/chat/completions');

		// cURLオプションを設定
		$options = [
			CURLOPT_POST => true,
			CURLOPT_HTTPHEADER => $header,
			CURLOPT_POSTFIELDS => $params,
			CURLOPT_RETURNTRANSFER => true,
		];

		// 計測開始
		$start_time = microtime(true);

		// タイムアウト時間を設定（秒単位）
		ini_set('max_execution_time', 180);
		
		// cURLオプションを適用
		curl_setopt_array($curl, $options);

		// APIリクエストを実行
		$response = curl_exec($curl);
		$httpcode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE); // HTTPレスポンスコードを取得
		$message = '';
		$text = '';
		$image_urls = [];
		$elapsed_time = 0;
		$error = null;

		// レスポンスが成功した場合
		if(200 == $httpcode)
		{
			$json_array = json_decode($response, true);
			$choices = $json_array['choices'];
			
			// APIからのメッセージを取得
			foreach ($choices as $c) {
				$message .= $c['message']['content'];
			}
			
			// 最後のメッセージの内容を処理
			if(is_array($last_message['content']))
			{
				$text = $last_message['content'][0]['text'];
				foreach ($last_message['content'] as $content) {
					if($content['type'] == 'image_url') {
						$image_urls[] = $content['image_url']['url'];
					}
				}
			}
			else
			{
				$text = $last_message['content'];
			}
			
			// ユーザのメッセージをデータベースに保存
			$data = [
				'chat_key' => $chat_key,
				'user_id' => $user_id,
				'message' => $text,
				'image_urls' => (count($image_urls) > 0) ? json_encode($image_urls) : null,
				'role' => 'user',
			];

			$this->loadModel('Message');
			$this->Message->create();
			$this->Message->save($data);

			// 計測終了
			$end_time = microtime(true);
			$elapsed_time = round($end_time - $start_time, 2);
			
			// AIの回答をデータベースに保存
			$data = [
				'chat_key' => $chat_key,
				'user_id' => $user_id,
				'message' => $message,
				'role' => 'assistant',
				'elapsed_time' => $elapsed_time,
			];
			
			$this->Message->create();
			$this->Message->save($data);

			// チャットの履歴を取得または作成
			$data = $this->fetchTable('Chat')->find()
				->where(['Chat.chat_key' => $chat_key, 'Chat.user_id' => $user_id])
				->first();
			
			if(!$data) {
				$data = [];
				$data['user_id'] = $user_id;
				$data['chat_key'] = $chat_key;
				$this->Chat->create();
			}
			
			$data['template_id'] = $template_id ? $template_id : 0;
			$data['title'] = '無題';
			
			// チャット履歴を保存
			$this->Chat->save($data);
		}
		else
		{
			// エラー処理
			$json_array = json_decode($response, true);
			$error = $json_array['error'];
		}

		// 結果をJSON形式で返す
		return json_encode([
			'elapsed_time' => $elapsed_time, 
			'message' => $message, 
			'httpcode' => $httpcode,
			'error' => $error,
		]);
	}

	/**
	 * チャットのタイトルを更新
	 *
	 * @return string 実行結果
	 */
	public function update()
	{
		$this->autoRender = FALSE;
		
		// AJAXリクエストかどうかを確認
		if(!$this->request->is('ajax'))
		{
			return false; // AJAXリクエストでない場合はfalseを返す
		}
		
		$user_id = $this->readAuthUser('id');

		$header = [
			'Authorization: Bearer '.Configure::read('api_key'),
			'Content-type: application/json',
		];
		
		$chat_key = $_POST['chat_key'];
		
		$messages = json_decode($_POST['messages'], true);
		//debug($messages);
		
		$params = json_encode([
			'model'			=> Configure::read('model'),
			'messages'		=> $messages,
			'temperature'	=> floatval(Configure::read('temperature')),
			'max_tokens'	=> floatval(Configure::read('max_tokens')),
			'top_p'			=> floatval(Configure::read('top_p')),
			'frequency_penalty'	=> floatval(Configure::read('frequency_penalty')),
			'presence_penalty'	=> floatval(Configure::read('presence_penalty'))
		]);

		$curl = curl_init('https://api.openai.com/v1/chat/completions');

		$options = [
			CURLOPT_POST => true,
			CURLOPT_HTTPHEADER =>$header,
			CURLOPT_POSTFIELDS => $params,
			CURLOPT_RETURNTRANSFER => true,
		];

		curl_setopt_array($curl, $options);

		$response = curl_exec($curl);

		$httpcode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);

		$text = '';

		if(200 == $httpcode)
		{
			$json_array = json_decode($response, true);
			$choices = $json_array['choices'];
			
			foreach($choices as $c)
			{
				$text .= $c['message']['content'];
			}

			// チャットの履歴
			$data = $this->fetchTable('Chat')->find()
				->where(['Chat.chat_key' => $chat_key, 'Chat.user_id' => $user_id])
				->first();

			if($data)
			{
				$text = str_replace('<br>', '', $text);
				$text = str_replace('「', '', $text);
				$text = str_replace('」', '', $text);
				$text = mb_strimwidth($text, 0, 40);
				
				$data['Chat']['title'] = $text;
				$this->Chat->save($data);
			}
		}
		else
		{
			debug($httpcode);
			debug($params);
			debug($response);
		}

		return h($text);
	}

	/**
	 * チャット履歴一覧を表示
	 */
	public function admin_index()
	{
		// SearchPluginの呼び出し
		$this->Prg->commonProcess();
		
		// モデルの filterArgs で定義した内容にしたがって検索条件を作成
		// ただし独自の検索条件は別途追加する必要がある
		$conditions = $this->Chat->parseCriteria($this->Prg->parsedParams());
		
		// 独自の検索条件
		$group_id	= $this->getQuery('group_id');
		$keyword	= $this->getQuery('keyword');
		
		// グループが指定されている場合、指定したグループに所属するユーザの履歴を抽出
		if($group_id != '')
			$conditions['User.id'] = $this->Group->getUserIdByGroupID($group_id);
		
		if($keyword != '')
			$conditions['Chat.title like'] = '%'.$keyword.'%';
		
		// 対象日時による絞り込み
		$from_date	= ($this->hasQuery('from_date')) ? implode('-', $this->getQuery('from_date')) : date('Y-m-d', strtotime('-1 month'));
		$to_date	= ($this->hasQuery('to_date'))	 ? implode('-', $this->getQuery('to_date'))   : date('Y-m-d');
		
		$conditions['Chat.created BETWEEN ? AND ?'] = [$from_date, $to_date.' 23:59:59'];
		
		// CSV出力モードの場合
		if($this->getQuery('cmd') == 'csv')
		{
			$this->autoRender = false;

			// メモリサイズ、タイムアウト時間を設定
			ini_set('memory_limit', '512M');
			ini_set('max_execution_time', (60 * 10));

			// Content-Typeを指定
			$this->response->type('csv');

			header('Content-Type: text/csv');
			header('Content-Disposition: attachment; filename="user_chats.csv"');
			
			$fp = fopen('php://output','w');
			
			$this->Chat->recursive = 0;
			
			$rows = $this->Chat->find()
				->where($conditions)
				->order('Chat.created desc')
				->all();
			
			$header = [
				__('ログインID'),
				__('氏名'),
				__('コース'),
				__('コンテンツ'),
				__('得点'),
				__('合格点'),
				__('結果'),
				__('理解度'),
				__('チャット時間'),
				__('チャット日時')
			];
			
			mb_convert_variables('SJIS-WIN', 'UTF-8', $header);
			fputcsv($fp, $header);
			
			foreach($rows as $row)
			{
				$row = [
					$row['User']['username'], 
					$row['User']['name'], 
					$row['Course']['title'], 
					$row['Content']['title'], 
					$row['Chat']['score'], 
					$row['Chat']['pass_score'], 
					Configure::read('chat_result.'.$row['Chat']['is_passed']), 
					Configure::read('chat_understanding.'.$row['Chat']['understanding']), 
					Utils::getHNSBySec($row['Chat']['study_sec']), 
					Utils::getYMDHN($row['Chat']['created']),
				];
				
				mb_convert_variables('SJIS-WIN', 'UTF-8', $row);
				
				fputcsv($fp, $row);
			}
			
			fclose($fp);
		}
		else
		{
			$this->Paginator->settings['conditions'] = $conditions;
			$this->Paginator->settings['order'] 	 = 'Chat.created desc';
			$this->Chat->recursive = 0;
			
			try
			{
				$chats = $this->paginate();
			}
			catch(Exception $e)
			{
				$this->request->params['named']['page']=1;
				$chats = $this->paginate();
			}
			
			$groups = $this->Group->find('list');
			
			$this->set(compact('chats', 'groups', 'group_id', 'from_date', 'to_date', 'keyword'));
		}
	}

	/**
	 * チャット履歴の削除
	 */
	public function delete($chat_key)
	{
		$user_id = $this->readAuthUser('id');
		
		$this->fetchTable('Chat')->deleteAll(['Chat.user_id' => $user_id, 'Chat.chat_key' => $chat_key], false);
		$this->fetchTable('Message')->deleteAll(['Chat.user_id' => $user_id, 'Message.chat_key' => $chat_key], false);

		return $this->redirect(['controller' => 'homes', 'action' => 'index']);
	}
}

