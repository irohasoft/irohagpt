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
 * Template Model
 *
 * @property Group $Group
 * @property Chat $Chat
 * @property ChatsMessage $ChatsMessage
 * @property Chat $Chat
 * @property User $User
 */
class Template extends AppModel
{
	public $order = "Template.sort_no"; // デフォルトのソート条件

	/**
	 * バリデーションルール
	 * https://book.cakephp.org/2/ja/models/data-validation.html
	 * @var array
	 */
	public $validate = [
		'title'   => ['notBlank' => ['rule' => ['notBlank']]],
		'sort_no' => ['numeric'  => ['rule' => ['numeric']]]
	];

	/**
	 * アソシエーションの設定
	 * https://book.cakephp.org/2/ja/models/associations-linking-models-together.html
	 * @var array
	 */
	public $hasMany = [
		'Chat' => [
			'className' => 'Chat',
			'foreignKey' => 'template_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		]
	];

	public $belongsTo = [
		'User' => [
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		]
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
		'title' => [
			'type' => 'like',
			'field' => 'Template.title'
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


	/**
	 * テンプレートの並べ替え
	 * 
	 * @param array $id_list テンプレートのIDリスト（並び順）
	 */
	public function setOrder($id_list)
	{
		for($i=0; $i< count($id_list); $i++)
		{
			$sql = "UPDATE ib_templates SET sort_no = :sort_no WHERE id= :id";

			$params = [
				'sort_no' => ($i + 1),
				'id' => $id_list[$i]
			];

			$this->query($sql, $params);
		}
	}
	
	public function getPopularTemplates($user_id)
	{
		$sql = <<<EOF
SELECT
	t.id,
	t.title,
	COUNT( c.template_id ) AS usage_count 
FROM
	ib_templates t
	LEFT JOIN ib_chats c ON t.id = c.template_id 
WHERE 
	t.user_id = :user_id
	AND
	t.is_master = 0
GROUP BY
	t.id 
ORDER BY
	usage_count DESC,
	t.sort_no 
	LIMIT 5;
EOF;
		$params = [
			'user_id'   => $user_id
		];
		$data = $this->query($sql, $params);
		
		$list = [];
		
		foreach($data as $row)
		{
			$list[] = ['id' => $row['t']['id'], 'title' => $row['t']['title']];
		}
		
		return $list;
	}
	
	/**
	 * テンプレートへのアクセス権限チェック
	 * 
	 * @param int $user_id   アクセス者のユーザID
	 * @param int $template_id アクセス先のテンプレートのID
	 * @return bool true: アクセス可能, false : アクセス不可
	 */
	public function hasRight($user_id, $template_id)
	{
		$has_right = false;
		
		$params = [
			'user_id'   => $user_id,
			'template_id' => $template_id
		];
		
		$sql = <<<EOF
SELECT count(*) as cnt
  FROM ib_templates
 WHERE template_id = :template_id
   AND user_id   = :user_id
EOF;
		$data = $this->query($sql, $params);
		
		if($data[0][0]['cnt'] > 0)
			$has_right = true;
		
		$sql = <<<EOF
SELECT count(*) as cnt
  FROM ib_groups_templates gc
 INNER JOIN ib_users_groups ug ON gc.group_id = ug.group_id AND ug.user_id   = :user_id
 WHERE gc.template_id = :template_id
EOF;
		$data = $this->query($sql, $params);
		
		if($data[0][0]["cnt"] > 0)
			$has_right = true;
		
		return $has_right;
	}
	
	/**
	 * テンプレートの削除
	 * 
	 * @param int $template_id 削除するテンプレートのID
	 */
	public function deleteTemplate($template_id)
	{
		$params = [
			'template_id' => $template_id
		];
		
		// テンプレートの削除
		$sql = "DELETE FROM ib_templates WHERE id = :template_id;";
		$this->query($sql, $params);
	}
}
