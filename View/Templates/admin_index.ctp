<?= $this->element('admin_menu');?>
<div class="admin-templates-index">
	<?php if($this->action == 'admin_master') {?>
	<div class="ib-page-title"><?= __('マスターテンプレート一覧'); ?></div>
	<div class="buttons_container">
		<button type="button" class="btn btn-primary btn-add" onclick="location.href='<?= Router::url(['action' => 'master_add']) ?>'">+ 追加</button>
	</div>
	<?php }else{?>
	<div class="ib-page-title"><?= __('テンプレート一覧'); ?></div>
	<div class="buttons_container">
		<button type="button" class="btn btn-default" onclick="location.href='<?= Router::url(['action' => 'master']) ?>'">マスターテンプレート</button>
	</div>
	<?php }?>
	
	<?php if(($this->action != 'admin_master')) {?>
	<br>
	<div class="ib-horizontal">
	<?php
		echo $this->Form->create('Template');
		
		echo '<div class="ib-row">';
		echo $this->Form->searchField('title',		['label' => __('テンプレート名')]);
		echo $this->Form->searchField('username',	['label' => __('ログインID')]);
		echo $this->Form->searchField('name',		['label' => __('氏名')]);
		echo '</div>';
		
		echo '<div class="ib-search-buttons">';
		echo $this->Form->submit(__('検索'),	['class' => 'btn btn-info', 'div' => false]);
		echo '</div>';
		
		echo $this->Form->end();
	?>
	</div>
	<?php }?>

	<table>
	<thead>
	<tr>
		<th><?= $this->Paginator->sort('title', 'テンプレート名'); ?></th>
		<th><?= $this->Paginator->sort('User.name', 'ログインID'); ?></th>
		<th><?= $this->Paginator->sort('User.name', '氏名'); ?></th>
		<th class="ib-col-date"><?= $this->Paginator->sort('Template.created', __('作成日時')); ?></th>
		<th class="ib-col-date"><?= $this->Paginator->sort('Template.modified', __('更新日時')); ?></th>
		<th class="ib-col-action"><?= __('Actions'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($templates as $template): ?>
	<tr>
		<td>
			<?= h($template['Template']['title']); ?>&nbsp;
			<?= $this->Form->hidden('id', ['id'=>'', 'class'=>'template_id', 'value'=>$template['Template']['id']]); ?>
		</td>
		<td><?= h($template['User']['username']); ?>&nbsp;</td>
		<td><?= h($template['User']['name']); ?>&nbsp;</td>
		<td class="ib-col-date"><?= h(Utils::getYMDHN($template['Template']['created'])); ?>&nbsp;</td>
		<td class="ib-col-date"><?= h(Utils::getYMDHN($template['Template']['modified'])); ?>&nbsp;</td>
		<td class="ib-col-action">
			<?php
			$action = ($this->action == 'admin_master') ?  'master_edit' : 'edit';
			?>
			<button type="button" class="btn btn-success" onclick="location.href='<?= Router::url(['action' => $action, $template['Template']['id']]) ?>'"><?= __('編集')?></button>
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
