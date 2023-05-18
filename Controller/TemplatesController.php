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
 * Templates Controller
 * https://book.cakephp.org/2/ja/controllers.html
 */
class TemplatesController extends AppController
{
	/**
	 * 使用するコンポーネント
	 * https://book.cakephp.org/2/ja/core-libraries/toc-components.html
	 */
	public $components = [
		'Paginator',
		'Security' => [
			'csrfUseOnce' => false,
			'unlockedActions' => ['order']
		],
	];

	public function index()
	{
		$user_id = $this->readAuthUser('id');
		$no_record = '';

		$templates = $this->fetchTable('Template')->find()
			->where(['Template.user_id' => $user_id])
			->order('Template.sort_no asc')
			->all();
		
		//debug($templates);
		$this->set(compact('templates', 'no_record'));
	}

	/**
	 * テンプレート一覧を表示
	 */
	public function admin_index()
	{
		$this->set('templates', $this->Paginator->paginate());
	}

	public function admin_edit($template_id = null)
	{
		if($this->isEditPage() && !$this->Template->exists($template_id))
		{
			throw new NotFoundException(__('Invalid template'));
		}
		
		if($this->request->is(['post', 'put']))
		{
			if(Configure::read('demo_mode'))
				return;
			
			if($this->Template->save($this->request->data))
			{
				$this->Flash->success(__('テンプレートが保存されました'));
				return $this->redirect(['action' => 'index']);
			}
			else
			{
				$this->Flash->error(__('The template could not be saved. Please, try again.'));
			}
		}
		else
		{
			$this->request->data = $this->Template->get($template_id);
		}
		
		$this->render('edit');
	}

	/**
	 * テンプレートの追加
	 */
	public function add()
	{
		$this->edit();
		$this->render('edit');
	}

	/**
	 * テンプレートの編集
	 * @param int $template_id テンプレートID
	 */
	public function edit($template_id = null)
	{
		if($this->isEditPage() && !$this->Template->exists($template_id))
		{
			throw new NotFoundException(__('Invalid template'));
		}
		
		if($this->request->is(['post', 'put']))
		{
			if(Configure::read('demo_mode'))
				return;
			
			// 作成者を設定
			$this->request->data['Template']['user_id'] = $this->readAuthUser('id');
			
			if($this->Template->save($this->request->data))
			{
				$this->Flash->success(__('テンプレートが保存されました'));
				return $this->redirect(['action' => 'index']);
			}
			else
			{
				$this->Flash->error(__('The template could not be saved. Please, try again.'));
			}
		}
		else
		{
			$this->request->data = $this->Template->get($template_id);
		}
	}

	/**
	 * テンプレートの削除
	 * @param int $template_id テンプレートID
	 */
	public function admin_delete($template_id = null)
	{
		if(Configure::read('demo_mode'))
			return;
		
		$this->Template->id = $template_id;
		if(!$this->Template->exists())
		{
			throw new NotFoundException(__('Invalid template'));
		}

		$this->request->allowMethod('post', 'delete');
		$this->Template->deleteTemplate($template_id);
		$this->Flash->success(__('テンプレートが削除されました'));

		return $this->redirect(['action' => 'index']);
	}

	/**
	 * Ajax によるテンプレートの並び替え
	 *
	 * @return string 実行結果
	 */
	public function order()
	{
		$this->autoRender = FALSE;
		if($this->request->is('ajax'))
		{
			$this->Template->setOrder($this->data['id_list']);
			return "OK";
		}
	}
}
