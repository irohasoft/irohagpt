<div class="templates-edit">
	<div class="breadcrumb">
<?php
if($this->isAdminPage())
{
	$action = 'index';
	
	// 管理者側
	if(
		($this->action == 'admin_master')||
		($this->action == 'admin_master_add')||
		($this->action == 'admin_master_edit')
	)
	{
		$action = 'master';
	}
	
	$this->Html->addCrumb(
		__('<< 戻る'),
		['controller' => 'templates','action' => $action],
	);

	echo $this->Html->getCrumbs(' / ');
}
else
{
	// 利用者側
	$this->Html->addCrumb(
		'<span class="glyphicon glyphicon-home" aria-hidden="true"></span> HOME',
		['controller' => 'homes','action' => 'index'],
		['escape' => false],
	);

	$this->Html->addCrumb(
		'<span class="glyphicon glyphicon-file" aria-hidden="true"></span> '.__('テンプレート一覧'),
		['controller' => 'templates','action' => 'index'],
		['escape' => false],
	);

	echo $this->Html->getCrumbs(' / ');
}
?>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<?= $this->isEditPage() ? __('編集') :  __('新規テンプレート'); ?>
		</div>
		<div class="panel-body">
		<?php
			echo $this->Form->create('Template', Configure::read('form_defaults'));
			echo $this->Form->input('id');
			echo $this->Form->input('title',		['label' => __('テンプレート名')]);
			echo $this->Form->inputExp('before_body',	['label' => __('メッセージの前に追加')], '※ [[選択肢A|選択肢B]]形式で記述することで選択肢を埋め込むことが可能です。<br>※ [[text]]と記述することでテキストフィールドを埋め込むことが可能です。');
			
			echo $this->Form->inputExp('body',			['label' => __('メッセージの入力画面に初期設定')], '※ メッセージ時に編集可能です。');
			echo $this->Form->input('after_body',	['label' => __('メッセージの後に追加')]);
			echo $this->Form->input('comment',		['label' => __('備考')]);
			echo Configure::read('form_submit_before')
				.$this->Form->submit(__('保存'), Configure::read('form_submit_defaults'))
				.Configure::read('form_submit_after');
			echo $this->Form->end();
		?>
		</div>
	</div>
</div>
