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
		'Search.Prg',
		'Security' => [
			'csrfUseOnce' => false,
			'unlockedFields' => ['ids'],
			'unlockedActions' => ['order']
		],
	];

	public function index()
	{
		$user_id = $this->readAuthUser('id');

		// 個人のテンプレート
		$templates = $this->fetchTable('Template')->find()
			->where(['Template.user_id' => $user_id])
			->order('Template.sort_no asc')
			->all();
		
		// マスターテンプレートの数
		$master_count = $this->fetchTable('Template')->find()
			->where(['Template.is_master' => 1])
			->count();
		
		//debug($templates);
		$this->set(compact('templates', 'master_count'));
	}

	/**
	 * テンプレート一覧を表示
	 */
	public function admin_index()
	{
		// SearchPluginの呼び出し
		$this->Prg->commonProcess();
		
		
		// Model の filterArgs に定義した内容にしたがって検索条件を作成
		$conditions = $this->Template->parseCriteria($this->Prg->parsedParams());
		
		$conditions['Template.is_master'] = null;
		$this->Paginator->settings['conditions'] = $conditions;
		
		$this->set('templates', $this->Paginator->paginate());
	}

	/**
	 * テンプレート一覧を表示
	 */
	public function admin_master()
	{
		$conditions['Template.is_master'] = 1;
		$this->Paginator->settings['conditions'] = $conditions;
		
		$this->set('templates', $this->Paginator->paginate());
		$this->render('admin_index');
	}

	public function admin_master_add()
	{
		$this->edit();
		$this->render('edit');
	}

	public function admin_master_edit($template_id = null)
	{
		$this->edit($template_id);
		$this->render('edit');
	}

	public function admin_edit($template_id = null)
	{
		$this->edit($template_id);
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
		
		// 所有者の確認（利用者側の編集画面のみ）
		if($this->isEditPage() && !$this->isAdminPage())
		{
			$template = $this->Template->get($template_id);
			
			// テンプレートの所有者と一致しない場合、アクセスを拒否
			if($template['Template']['user_id'] != $this->readAuthUser('id'))
			{
				throw new NotFoundException(__('Invalid access'));
			}
		}
		
		if($this->request->is(['post', 'put']))
		{
			if(Configure::read('demo_mode'))
				return;
			
			// 作成時の場合、作成者を設定
			if(!$this->isEditPage())
				$this->request->data['Template']['user_id'] = $this->readAuthUser('id');
			
			// マスターテンプレートの場合、フラグをオンにする
			$is_master = ($this->action == 'admin_master_add')||($this->action == 'admin_master_edit');
			
			if($is_master)
				$this->request->data['Template']['is_master'] = 1;
			
			if($this->Template->save($this->request->data))
			{
				$this->Flash->success(__('テンプレートが保存されました'));
				
				if($is_master)
				{
					return $this->redirect(['action' => 'master']);
				}
				else
				{
					return $this->redirect(['action' => 'index']);
				}
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

		$template = $this->Template->findById($template_id);
		
		$this->request->allowMethod('post', 'delete');
		$this->Template->deleteTemplate($template_id);
		$this->Flash->success(__('テンプレートが削除されました'));

		if($template['Template']['is_master'] == 1)
		{
			return $this->redirect(['action' => 'master']);
		}
		else
		{
			return $this->redirect(['action' => 'index']);
		}
	}

	/**
	 * テンプレートの削除
	 * @param int $template_id テンプレートID
	 */
	public function delete($template_id = null)
	{
		if(Configure::read('demo_mode'))
			return;
		
		$this->Template->id = $template_id;
		if(!$this->Template->exists())
		{
			throw new NotFoundException(__('Invalid template'));
		}

		$template = $this->Template->findById($template_id);
		
		// テンプレートの所有を確認
		if($template['Template']['user_id'] != $this->readAuthUser('id'))
		{
			$this->Flash->error(__('テンプレートを所有していません'));
			return $this->redirect(['action' => 'index']);
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

	public function copy()
	{
		$templates = $this->Template->find()->where(['Template.is_master' => 1])->all();
		
		if($this->request->is(['post', 'put']))
		{
			$ids = $this->getData('Template')['ids'];
			
			$list = explode(',', $ids);
			
			foreach($list as $template_id)
			{
				$data = $this->Template->get($template_id);
				
				if($data['Template']['is_master'] != 1)
					continue;
				
				$data['Template']['id'] = null;
				$data['Template']['user_id'] = $this->readAuthUser('id');
				$data['Template']['sort_no'] = 0;
				$data['Template']['is_master'] = null;
				$data['Template']['created'] = null;
				$data['Template']['modified'] = null;
				
				$this->Template->save($data);
				
				$this->Flash->success(__('テンプレートをコピーしました'));
				
				return $this->redirect(['action' => 'index']);
			}
		}
		
		$this->set('templates', $templates);
	}
}
