<?= $this->Html->css('messages.css?20230501'); ?>
<?php $this->start('script-embedded'); ?>
<script>
	var API_URL  = '<?= Router::url(['controller' => 'chats', 'action' => 'api', 'admin' => false]);?>';
	var UPDATE_API_URL = '<?= Router::url(['controller' => 'chats', 'action' => 'update', 'admin' => false]);?>';
	var CHAT_KEY = '<?= $chat_key;?>';
	var TEMPLATE_ID = '<?= $template_id;?>';
	var MESSAGE_URL = '<?= Router::url(['controller' => 'messages', 'action' => 'index', 'admin' => false, Utils::getNewPassword(8)]); ?>';
	var IMAGE_UPLOAD_URL = '<?= Router::url(['controller' => 'homes', 'action' => 'upload_image'])?>';
</script>
<?php $this->end(); ?>
<?= $this->Html->script('prompt.js?20241001');?>
<?= $this->Html->script('messages.js?20241001'); ?>
<?php $this->start('css-embedded'); ?>
<style>
<?php if($this->isAdminPage()) { // 管理システムからの表示の場合、メニューを非表示 ?>
.ib-navi-item
{
	display					: none;
}

.ib-logo a
{
	pointer-events			: none;
}
<?php }?>
</style>
<?php $this->end(); ?>
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
				<div class="alert alert-<?= ($message['Message']['role'] == 'user') ? 'success' : 'warning' ; ?> msg msg-<?= $message['Message']['role']; ?>"
					 <?php if (!empty($message['Message']['image_urls'])): ?>
					 data-image-url="<?= h($message['Message']['image_urls']); ?>"
					 <?php endif; ?>>
					<?= nl2br(h($message['Message']['message'])); ?>
				</div>
				<?php if ($message['Message']['role'] == 'assistant') {?>
				<div class="elapsed-time"><?= $message['Message']['elapsed_time'] ?>秒</div>
				<?php }?>
				<?php endforeach; ?>
				<button class="btn btn-default" onclick="continueToChat();">続けてください</button>
				<div class="form-group">
					<div class="col">
						<!--prompt.ctp-->
						<?= $this->element('prompt', ['message' => $first_message, 'image_urls' => h($first_image_urls)]);?>
					</div>
				</div>
			<?php } else {?>
				<div class="stage">
					<?php foreach ($messages as $message): ?>
						<?php
						$img_tag = '';

						// 画像を表示
						if($message['Message']['image_urls'])
						{
							$image_urls = json_decode($message['Message']['image_urls']);
							$img_tag = '<div class="stored-images-container">';

							foreach ($image_urls as $image_url)
							{
								$img_tag .= '<div class="stored-image-container"><img src ="'.h($image_url).'" class="stored-image"></div>';
							}

							$img_tag .= '</div>';
						}
						?>
						<div class="alert alert-<?= ($message['Message']['role'] == 'user') ? 'success' : 'warning' ; ?> msg msg-<?= $message['Message']['role']; ?>"
							 <?php if (!empty($message['Message']['image_urls'])): ?>
							 data-image-url="<?= h($message['Message']['image_urls']); ?>"
							 <?php endif; ?>><?= $img_tag ?><?= nl2br(h($message['Message']['message'])); ?><!--表示が崩れるため、出力結果に改行を追加しない-->
						</div>
						<?php if ($message['Message']['role'] == 'assistant') {?>
						<div class="elapsed-time"><?= $message['Message']['elapsed_time'] ?>秒</div>
						<?php }?>
					<?php endforeach; ?>
				</div>
				<?php if(!$this->isAdminPage()) {?>
				<button class="btn btn-default" onclick="continueToChat();">続けてください</button>
				<!--prompt.ctp-->
				<?= $this->element('prompt', ['message' => '', 'image_urls' => '']);?>
				<?php }?>
			<?php }?>
		</div>
	</div>

	<div id="loading">
		<div class="spinner"></div>
		<div class="text-warning">AI is generating the answer...</div>
	</div>

</div>
