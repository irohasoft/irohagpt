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
 * Groups Controller
 * https://book.cakephp.org/2/ja/controllers.html
 */
class GroupsController extends AppController
{
	/**
	 * 使用するコンポーネント
	 * https://book.cakephp.org/2/ja/core-libraries/toc-components.html
	 */
	public $components = [
		'Paginator',
		'Security' => [
			'csrfUseOnce' => false,
		],
	];

	/**
	 * グループ一覧を表示
	 */
	public function admin_index()
	{
		$this->Group->recursive = 0;
		$this->Group->virtualFields['template_title'] = 'GroupTemplate.template_title'; // 外部結合テーブルのフィールドによるソート用
		
		$this->Paginator->settings = [
			'fields' => ['*', 'GroupTemplate.template_title'],
			'limit' => 20,
			'order' => 'created desc',
			'joins' => [
				['type' => 'LEFT OUTER', 'alias' => 'GroupTemplate',
						'table' => '(SELECT gc.group_id, group_concat(c.title order by c.id SEPARATOR \', \') as template_title FROM ib_groups_templates gc INNER JOIN ib_templates c ON c.id = gc.template_id  GROUP BY gc.group_id)',
						'conditions' => 'Group.id = GroupTemplate.group_id']
			]
		];
		
		$this->set('groups', $this->Paginator->paginate());
	}

	/**
	 * グループの追加
	 */
	public function admin_add()
	{
		$this->admin_edit();
		$this->render('admin_edit');
	}

	/**
	 * グループの編集
	 * @param int $group_id 編集するグループのID
	 */
	public function admin_edit($group_id = null)
	{
		if($this->isEditPage() && !$this->Group->exists($group_id))
		{
			throw new NotFoundException(__('Invalid group'));
		}
		
		if($this->request->is(['post', 'put']))
		{
			if($this->Group->save($this->request->data))
			{
				$this->Flash->success(__('グループ情報を保存しました'));
				return $this->redirect(['action' => 'index']);
			}
			else
			{
				$this->Flash->error(__('The group could not be saved. Please, try again.'));
			}
		}
		else
		{
			$this->request->data = $this->Group->get($group_id);
		}
		
		$templates = $this->Group->Template->find('list');
		$this->set(compact('templates'));
	}

	/**
	 * グループの削除
	 * @param int $group_id 削除するグループのID
	 */
	public function admin_delete($group_id = null)
	{
		$this->Group->id = $group_id;
		
		if(!$this->Group->exists())
		{
			throw new NotFoundException(__('Invalid group'));
		}
		
		$this->request->allowMethod('post', 'delete');
		
		if($this->Group->delete())
		{
			$this->Flash->success(__('グループ情報を削除しました'));
		}
		else
		{
			$this->Flash->error(__('The group could not be deleted. Please, try again.'));
		}
		
		return $this->redirect(['action' => 'index']);
	}
}
