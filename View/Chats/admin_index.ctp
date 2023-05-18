<?= $this->element('admin_menu');?>
<?php $this->start('script-embedded'); ?>
<script>
	function openChat(chat_key, user_id)
	{
		window.open(
			'<?= Router::url(['controller' => 'messages', 'action' => 'index']) ?>/index/'+chat_key+'/'+user_id,
			'irohaboard_chat',
			'width=1100, height=700, menubar=no, toolbar=no, scrollbars=yes'
		);
	}
	
	function downloadCSV()
	{
		$("#ChatCmd").val("csv");
		$("#ChatAdminIndexForm").submit();
		$("#ChatCmd").val("");
	}
</script>
<?php $this->end(); ?>
<div class="admin-chats-index">
	<div class="ib-page-title"><?= __('チャット履歴一覧'); ?></div>
	<div class="ib-horizontal">
	<?php
		echo $this->Form->create('Chat');
		/*
		echo '<div class="ib-row">';
		echo $this->Form->searchField('keyword',		['label' => __('キーワード'), 'value' => $keyword]);
		echo '</div>';
		*/
		
		echo '<div class="ib-row">';
		echo $this->Form->searchField('group_id',	['label' => __('グループ'), 'options' => $groups, 'empty' => '全て', 'selected' => $group_id]);
		echo $this->Form->searchField('username',	['label' => __('ログインID')]);
		echo $this->Form->searchField('name',		['label' => __('氏名')]);
		echo '</div>';
		
		echo '<div class="ib-search-buttons">';
		echo $this->Form->submit(__('検索'),	['class' => 'btn btn-info', 'div' => false]);
		echo $this->Form->hidden('cmd');
		//echo '<button type="button" class="btn btn-default" onclick="downloadCSV()">'.__('CSV出力').'</button>';
		echo '</div>';
		
		echo '<div class="ib-search-date-container">';
		echo $this->Form->searchDate('from_date', ['label'=> __('対象日時'), 'value' => $from_date]);
		echo $this->Form->searchDate('to_date',   ['label'=> __('～'), 'value' => $to_date]);
		echo '</div>';
		
		
		echo $this->Form->end();
	?>
	</div>
	<table cellpadding="0" cellspacing="0">
	<thead>
	<tr>
		<th nowrap><?= $this->Paginator->sort('User.username', __('ログインID')); ?></th>
		<th nowrap><?= $this->Paginator->sort('User.name', __('氏名')); ?></th>
		<th nowrap><?= $this->Paginator->sort('title', __('タイトル')); ?></th>
		<th class="ib-col-datetime"><?= $this->Paginator->sort('created', __('チャット日時')); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($chats as $chat): ?>
	<tr>
		<td><?= h($chat['User']['username']); ?>&nbsp;</td>
		<td><?= h($chat['User']['name']); ?>&nbsp;</td>
		<td><a href="javascript:openChat('<?= h($chat['Chat']['chat_key']); ?>', <?= h($chat['User']['id']); ?>);"><?= h($chat['Chat']['title']); ?></a></td>
		<td class="ib-col-date"><?= h(Utils::getYMDHN($chat['Chat']['created'])); ?>&nbsp;</td>
	</tr>
	<?php endforeach; ?>
	</tbody>
	</table>
	<?= $this->element('paging');?>
</div>
