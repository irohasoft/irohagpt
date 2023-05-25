<div class="admin-templates-edit">
<?php
// 利用者側
$param = ['controller' => 'homes', 'action' => 'index'];

// 管理者側
if($this->isAdminPage())
{
	// マスターテンプレートの場合
	if(
		($this->action == 'admin_master')||
		($this->action == 'admin_master_edit')
	)
	{
		$param = ['action' => 'master'];
	}
	else
	{
		$param = ['action' => 'index'];
	}
}

?>
<?= $this->Html->link(__('<< 戻る'), $param)?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<?= $this->isEditPage() ? __('編集') :  __('新規テンプレート'); ?>
		</div>
		<div class="panel-body">
		<?php
			echo $this->Form->create('Template', Configure::read('form_defaults'));
			echo $this->Form->input('id');
			echo $this->Form->input('title',		['label' => __('テンプレート名')]);
			echo $this->Form->inputExp('before_body',	['label' => __('プロンプトの前に追加')], '※ [[選択肢A|選択肢B]]形式で記述することで選択肢を埋め込むことが可能です。<br>※ [[text]]と記述することでテキストフィールドを埋め込むことが可能です。');
			
			echo $this->Form->inputExp('body',			['label' => __('プロンプトの入力画面に初期設定')], '※ プロンプト時に編集可能です。');
			echo $this->Form->input('after_body',	['label' => __('プロンプトの後に追加')]);
			echo $this->Form->input('comment',		['label' => __('備考')]);
			echo Configure::read('form_submit_before')
				.$this->Form->submit(__('保存'), Configure::read('form_submit_defaults'))
				.Configure::read('form_submit_after');
			echo $this->Form->end();
		?>
		</div>
	</div>
</div>
