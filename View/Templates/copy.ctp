<?php $this->start('script-embedded'); ?>
<script>
	$(document).ready(function()
	{
	});

	// custom 選択ユーザを削除
	function copyTemplates()
	{
		var list = new Array();
		
		$('.chk-add:checked').each(function(index)
		{
			list[list.length] = this.value;
		});
		
		if(list.length == 0)
		{
			alert("コピーするテンプレートが選択されていません");
			return;
		}
		
		if(!confirm('選択されたテンプレートをコピーしてもよろしいですか?'))
			return;
		
		$('#TemplateIds').val(list.join(','));
		$('#TemplateCopyForm').submit();
	}
</script>
<?php $this->end(); ?>

<div class="ib-breadcrumb">
	<?php
	$this->Html->addCrumb('<< '.__('ホーム'), [
		'controller' => 'homes',
		'action' => 'index'
	]);

	$this->Html->addCrumb(__('テンプレート一覧'), [
		'controller' => 'templates',
		'action' => 'index'
	]);

	echo $this->Html->getCrumbs(' / ');
	?>
</div>

<div class="admin-templates-index">
	<div class="panel panel-default">
		<div class="panel-heading"><?= __('マスターテンプレート一覧'); ?></div>
		<div class="panel-body">
			<div class="buttons_container">
			</div>
			
			<div>
				<p>
					<?= __('利用したいテンプレートを選択し、コピーボタンをクリックしてください。'); ?>
					<?= __('マスターテンプレートはシステム内の共通のテンプレートで、管理者のみ編集可能です。'); ?>
				</p>
				<button type="button" class="btn btn-primary btn-add" onclick="copyTemplates();">コピー</button>
			</div>
			
			<br>
			<ul class="list-group">
				<?php foreach ($templates as $template): ?>
				<div class="list-group-item">
					<?= $this->Form->hidden('id', ['id'=>'', 'class'=>'template_id', 'value'=>$template['Template']['id']]);?>
					<div class="list-group-item-heading display-3">
					<input type="checkbox" class="chk-add" name="chk-<?= h($template['Template']['id']); ?>" value="<?= h($template['Template']['id']); ?>" >
					<b><?= h($template['Template']['title']);?></b></div>
				</div>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</div>

<?php
	echo $this->Form->create('Template', Configure::read('form_defaults'));
	echo $this->Form->hidden('ids');
	echo $this->Form->end();
?>