<?= $this->Html->css('homes.css?20230501'); ?>
<?= $this->Html->script('jquery.pagination.js');?>
<?php $this->start('script-embedded'); ?>
<script>
	var MESSAGE_URL  = '<?= Router::url(['controller' => 'messages', 'action' => 'index']);?>/index/';
	var CHAT_DELETE_URL  = '<?= Router::url(['controller' => 'chats', 'action' => 'delete'])?>';
	var IMAGE_UPLOAD_URL	= '<?= Router::url(['controller' => 'homes', 'action' => 'upload_image'])?>';
</script>
<?php $this->end(); ?>
<?= $this->Html->script('prompt.js?20240901');?>
<?= $this->Html->script('homes.js?20240901');?>
<div class="users-templates-index">
	<div class="panel panel-success">
		<div class="panel-heading"><?= __('お知らせ'); ?></div>
		<div class="panel-body">
			<?php if($info != ''){?>
			<div class="well">
			<?php
				$target = Configure::read('open_link_same_window') ? [] : ['target' => '_blank'];
				$info = $this->Text->autoLinkUrls($info, $target);
				$info = nl2br($info);
				echo $info;
			?>
			</div>
			<?php }?>
			
			<?php if(count($infos) > 0){?>
			<table cellpadding="0" cellspacing="0">
			<tbody>
			<?php foreach ($infos as $info): ?>
			<tr>
				<td width="100" valign="top"><?= h(Utils::getYMD($info['Info']['created'])); ?></td>
				<td><?= $this->Html->link($info['Info']['title'], ['controller' => 'infos', 'action' => 'view', $info['Info']['id']]); ?></td>
			</tr>
			<?php endforeach; ?>
			</tbody>
			</table>
			<div class="text-right"><?= $this->Html->link(__('一覧を表示'), ['controller' => 'infos', 'action' => 'index']); ?></div>
			<?php }?>
			<?= $no_info;?>
		</div>
	</div>
	<div class="panel panel-info">
		<div class="panel-heading"><?= __('チャット'); ?></div>
		<div class="panel-body">
			<form method="post" class="template-form">
				<label><?= __('テンプレート'); ?></label>
				<div class="horizontal">
				<select class="form-control" id="TemplateId" name="template_id" onchange="changeTemplate($('#TemplateId').val());">
					<option value=""><?= __('テンプレートを使用しない'); ?></option>
					<?php foreach ($templates as $row): ?>
						<option value="<?= h($row['Template']['id']);?>" <?= ($row['Template']['id'] == $template_id) ? 'selected' : ''?>><?= h($row['Template']['title']);?></option>
					<?php endforeach; ?>
				</select>
				<input type="hidden" id="hidTemplateId" name="template_id" value="<?= h($template_id)?>" />
				<!--
				<button class="btn btn-primary" onclick="location.href='<?= Router::url(['controller' => 'messages', 'action' => 'index', Utils::getNewPassword(8)]);?>/' + $('#TemplateId').val()">+ 新規チャット</button>
				-->
				<button class="btn btn-default" onclick="location.href='<?= Router::url(['controller' => 'templates', 'action' => 'index']);?>'; return false;"><?= __('テンプレート管理'); ?></button>
				</div>
				<h5><?= __('よく使うテンプレート'); ?></h5>
				<div>
					<?php if($template_id){?>
						<a href="#" onclick="changeTemplate()">×<?= __('選択を解除'); ?></a>　
					<?php }?>
					<?php foreach ($popular_templates as $row): ?>
						<?php
						$title = h($row['title']);

						if($row['id'] == $template_id)
							$title = '<b>'.$title.'</b>';
						?>
						<a href="#" onclick="changeTemplate(<?= h($row['id']);?>)"><?= $title?></a>　
					<?php endforeach; ?>
				</div>
				<h5><?= __('新規チャット'); ?></h5>
				<?php if(($template) && ($template['Template']['before_body'] != '')) {?>
					<?php
						$body = h($template['Template']['before_body']);
						$body = nl2br($body);
						$body = Utils::convertStringToElement($body);
					?>
					<div class="well before-body"><?= $body?></div>
				<?php }?>
				<!--prompt.ctp-->
				<?= $this->element('prompt', ['message' => (($template) ? $template['Template']['body'] : ''), 'image_urls' => null]);?>
				<?php if(($template) && ($template['Template']['after_body'] != '')) {?>
					<div class="well after-body">
					<?php
						$body = $this->Text->autoLinkUrls($template['Template']['after_body'], [ 'target' => '_blank']);
						$body = nl2br($body);
						echo $body;
					?>
					</div>
				<?php }?>
			</form>
			<hr>
			<label><?= __('チャットの履歴'); ?></label>

			<ul class="list-group">
				<?php foreach ($chats as $chat): ?>
				<?php //debug($template)?>
					<a href="#" class="list-group-item"  onclick="location.href='<?= Router::url(['controller' => 'messages', 'action' => 'index', $chat['Chat']['chat_key']]);?>'">
						<button type="button" class="btn btn-danger pull-right" onclick="deleteChat('<?= $chat['Chat']['chat_key']?>');">削除</button>
						<div class="list-group-item-heading display-3"><b><?= h($chat['Chat']['title']);?></b></div>
						<p class="list-group-item-text">
							<span class="first-date"><?= __('作成').': '.Utils::getYMD($chat['Chat']['created']); ?></span>
							<span class="last-date"><?= __('更新').': '.Utils::getYMD($chat['Chat']['modified']); ?></span>
						</p>
					</a>
				<?php endforeach; ?>
				<?= $no_record;?>
			</ul>
		</div>
	</div>

</div>
