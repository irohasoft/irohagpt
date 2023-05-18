<?= $this->Html->css('messages.css?20230501'); ?>
<?php $this->start('script-embedded'); ?>
<script>
	var API_URL  = '<?= Router::url(['controller' => 'chats', 'action' => 'api', 'admin' => false]);?>';
	var UPDATE_API_URL = '<?= Router::url(['controller' => 'chats', 'action' => 'update', 'admin' => false]);?>';
	var CHAT_KEY = '<?= $chat_key;?>';
	var TEMPLATE_ID = '<?= $template_id;?>';
	var MESSAGE_URL = '<?= Router::url(['controller' => 'messages', 'action' => 'index', 'admin' => false, Utils::getNewPassword(8)]); ?>';
</script>
<?php $this->end(); ?>
<?= $this->Html->script('messages.js?20230502'); ?>
<div class="messages-index">
	<div class="ib-breadcrumb">
	<?php
	// 管理者によるチャット履歴表示の場合、パンくずリストを表示しない
	if(!$this->isAdminPage())
	{
		$this->Html->addCrumb('<< '.__('ホーム'), [
			'controller' => 'homes',
			'action' => 'index'
		]);
		echo $this->Html->getCrumbs(' / ');
	}

	$chat_title = '新規チャット';

	if($chat)
		$chat_title = $chat['Chat']['title'];

	?>
	</div>

	<div class="panel panel-info">
		<div class="panel-heading"><?= h($chat_title); ?></div>
		<div class="panel-body">
			<?php if(count($messages) == 0) { // 新規チャット?>
				<div class="form-group">
					<div class="col">
						<div class="stage"></div>
					</div>
				</div>

				<?php foreach ($messages as $message): ?>
				<div class="alert alert-<?= ($message['Message']['role'] == 'user') ? 'success' : 'warning' ; ?> msg msg-<?= $message['Message']['role']; ?>"><?= $message['Message']['message']; ?></div>
				<?php endforeach; ?>
				<button class="btn btn-default" onclick="continueToChat();">続けてください</button>
				<div class="form-group">
					<div class="col">
						<textarea class="form-control text-question" maxlength="<?= Configure::read('prompt_max') ?>" type="text"="required" placeholder="質問を入力し、エンターキーを押下してください。
改行する場合は、Shift+エンターキーを押下してください。"><?= $first_message?></textarea>
					</div>
				</div>
			<?php } else {?>
				<div class="stage">
					<?php foreach ($messages as $message): ?>
						<div class="alert alert-<?= ($message['Message']['role'] == 'user') ? 'success' : 'warning' ; ?> msg msg-<?= $message['Message']['role']; ?>"><?= nl2br(h($message['Message']['message'])); ?></div>
						<?php if ($message['Message']['role'] == 'assistant') {?>
						<div class="elapsed-time"><?= $message['Message']['elapsed_time'] ?>秒</div>
						<?php }?>
					<?php endforeach; ?>
				</div>
				<?php if(!$this->isAdminPage()) {?>
				<button class="btn btn-default" onclick="continueToChat();">続けてください</button>
				<textarea class="form-control text-question" maxlength="<?= Configure::read('prompt_max') ?>" type="text"="required" placeholder="質問を入力し、エンターキーを押下してください。
改行する場合は、Shift+エンターキーを押下してください。"></textarea>
				<?php }?>
			<?php }?>
		</div>
	</div>

	<div id="loading">
		<div class="spinner"></div>
		<div class="text-warning">AI is generating the answer...</div>
	</div>

</div>
