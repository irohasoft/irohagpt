<?php
/**
 * iroha GPT Project
 *
 * @author        Kotaro Miura
 * @copyright     2015-2021 iroha Soft, Inc. (https://irohasoft.jp)
 * @link          https://irohaboard.irohasoft.jp
 * @license       https://www.gnu.org/licenses/gpl-3.0.en.html GPL License
 */

App::uses('AppController', 'Controller');

/**
 * Homes Controller
 * https://book.cakephp.org/2/ja/controllers.html
 */
class HomesController extends AppController
{
	/**
	 * テンプレート一覧（ホーム画面）を表示
	 */
	public function index()
	{
		$user_id = $this->readAuthUser('id');
		$template_id = $this->getData('template_id');
		//debug($template_id);
		
		// 全体のお知らせの取得
		$data = $this->Setting->find()
			->where(['Setting.setting_key' => 'information'])
			->first();
		
		$info = $data['Setting']['setting_value'];
		
		// お知らせ一覧を取得
		$infos = $this->fetchTable('Info')->getInfos($user_id, 2);
		
		$no_info = '';
		
		// 全体のお知らせもお知らせも存在しない場合
		if(($info == '') && (count($infos) == 0))
			$no_info = __('お知らせはありません');
		
		$no_record = '';
		
		$templates = $this->fetchTable('Template')->find()
			->where(['Template.user_id' => $user_id])
			->order('Template.sort_no asc')
			->all();
		/*
		$chats = $this->fetchTable('Message')->find('all', array(
			'fields' => ['chat_key', 'created', "LEFT(GROUP_CONCAT(message SEPARATOR ','), 20) AS message"],
			'group' => 'chat_key',
			'order' => 'Message.id'
		));
		*/
		//debug($chats);
		$template = $this->fetchTable('Template')->find()
			->where(['Template.user_id' => $user_id, 'Template.id' => $template_id])
			->first();
		//debug($template);
		
		$popular_templates = $this->fetchTable('Template')->getPopularTemplates($user_id);
		//debug($popular_templates);

		$chats = $this->fetchTable('Chat')->find()
			->where(['Chat.user_id' => $user_id])
			->order(['Chat.modified desc'])
			->all();

		if(count($templates) == 0)
			$no_record = __('表示可能なテンプレートはありません');
		
		$this->set(compact('templates', 'template', 'template_id', 'popular_templates', 'chats', 'no_record', 'info', 'infos', 'no_info'));
	}
}
