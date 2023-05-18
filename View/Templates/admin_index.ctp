<?= $this->element('admin_menu');?>
<div class="admin-templates-index">
	<div class="ib-page-title"><?= __('テンプレート一覧'); ?></div>
	<div class="buttons_container">
		<button type="button" class="btn btn-primary btn-add" onclick="location.href='<?= Router::url(['action' => 'add']) ?>'">+ 追加</button>
	</div>

	<table>
	<thead>
	<tr>
		<th><?= $this->Paginator->sort('title', 'テンプレート名'); ?></th>
		<th nowrap class="col-template"><?= $this->Paginator->sort('User.name', '作成者'); ?></th>
		<th class="ib-col-date"><?= $this->Paginator->sort('Template.created', __('作成日時')); ?></th>
		<th class="ib-col-date"><?= $this->Paginator->sort('Template.modified', __('更新日時')); ?></th>
		<th class="ib-col-action"><?= __('Actions'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($templates as $template): ?>
	<tr>
		<td>
		<?php 
			echo $template['Template']['title'];
			echo $this->Form->hidden('id', ['id'=>'', 'class'=>'template_id', 'value'=>$template['Template']['id']]);
		?>
		</td>
		<td class="ib-col-date"><?= h($template['User']['name']); ?>&nbsp;</td>
		<td class="ib-col-date"><?= h(Utils::getYMDHN($template['Template']['created'])); ?>&nbsp;</td>
		<td class="ib-col-date"><?= h(Utils::getYMDHN($template['Template']['modified'])); ?>&nbsp;</td>
		<td class="ib-col-action">
			<button type="button" class="btn btn-success" onclick="location.href='<?= Router::url(['action' => 'edit', $template['Template']['id']]) ?>'"><?= __('編集')?></button>
			<?php if($loginedUser['role'] == 'admin') {?>
			<?= $this->Form->postLink(__('削除'), ['action' => 'delete', $template['Template']['id']], ['class'=>'btn btn-danger'], 
				__('[%s] を削除してもよろしいですか?', $template['Template']['title']));?>
			<?php }?>
		</td>
	</tr>
	<?php endforeach; ?>
	</tbody>
	</table>
</div>
