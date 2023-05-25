<?php $this->start('css-embedded'); ?>
<style>
#sortable-table
{
	cursor: move;
}

</style>
<?php $this->end(); ?>
<?php $this->start('script-embedded'); ?>
<script>
	$(function(){
		$('#sortable-table').sortable(
		{
			helper: function(event, ui)
			{
				var children = ui.children();
				var clone = ui.clone();

				clone.children().each(function(index)
				{
					$(this).width(children.eq(index).width());
				});
				return clone;
			},
			update: function(event, ui)
			{
				var id_list = new Array();

				$('.template_id').each(function(index)
				{
					id_list[id_list.length] = $(this).val();
				});

				$.ajax({
					url: "<?= Router::url(['action' => 'order']) ?>",
					type: "POST",
					data: { id_list : id_list },
					dataType: "text",
					success : function(response){
						//通信成功時の処理
						//alert(response);
					},
					error: function(){
						//通信失敗時の処理
						//alert('通信失敗');
					}
				});
			},
			cursor: "move",
			opacity: 0.5
		});
	});
</script>
<?php $this->end(); ?>
<div class="ib-breadcrumb">
	<?php
	$this->Html->addCrumb('<< '.__('ホーム'), [
		'controller' => 'homes',
		'action' => 'index'
	]);
	echo $this->Html->getCrumbs(' / ');
	?>
</div>

<div class="admin-templates-index">
	<div class="panel panel-default">
		<div class="panel-heading"><?= __('テンプレート一覧'); ?></div>
		<div class="panel-body">
			<div class="buttons_container">
				<?php if($master_count > 0) {?>
				<button type="button" class="btn btn-default" onclick="location.href='<?= Router::url(['action' => 'copy']) ?>'"><?= __('マスターテンプレートからコピー'); ?></button>
				<?php }?>
				<button type="button" class="btn btn-primary btn-add" onclick="location.href='<?= Router::url(['action' => 'add']) ?>'">+ 追加</button>
			</div>

			<?php if(count($templates) > 0) {?>
			<div class="alert alert-warning"><?= __('ドラッグアンドドロップでテンプレートの並び順が変更できます。'); ?></div>
			<ul id='sortable-table' class="list-group">
				<?php foreach ($templates as $template): ?>
				<div class="list-group-item">
					<?= $this->Form->hidden('id', ['id'=>'', 'class'=>'template_id', 'value'=>$template['Template']['id']]);?>
					
					<?php if($loginedUser['role'] == 'admin') {?>
					<?= $this->Form->postLink(__('削除'), ['action' => 'delete', $template['Template']['id']], ['class'=>'btn btn-danger pull-right'], 
						__('[%s] を削除してもよろしいですか?', $template['Template']['title']));?>
					<?php }?>
					<button type="button" class="btn btn-success pull-right" style="margin-right:5px;" onclick="location.href='<?= Router::url(['action' => 'edit', $template['Template']['id']]) ?>'"><?= __('編集')?></button>
					
					<div class="list-group-item-heading display-3"><b><?= h($template['Template']['title']);?></b></div>
					<p class="list-group-item-text">
						<span class="first-date"><?= __('作成').': '.Utils::getYMD($template['Template']['created']); ?></span>
						<span class="last-date"><?= __('更新').': '.Utils::getYMD($template['Template']['modified']); ?></span>
					</p>
				</div>
				<?php endforeach; ?>
			</ul>
			<?php } else {?>
			<?= __('テンプレートは存在しません。'); ?>
			<?php }?>
		</div>
	</div>

