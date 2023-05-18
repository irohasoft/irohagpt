<div class="infos-view">
	<div class="breadcrumb">
	<?php
	$this->Html->addCrumb('HOME', [
			'controller' => 'homes',
			'action' => 'index'
	]);

	$this->Html->addCrumb(__('お知らせ一覧'), [
		'controller' => 'infos',
		'action' => 'index'
	]);

	echo $this->Html->getCrumbs(' / ');
	
	$title = h($info['Info']['title']);
	$date  = h(Utils::getYMD($info['Info']['created']));
	$body  = $info['Info']['body'];
	$target = Configure::read('open_link_same_window') ? [] : ['target' => '_blank'];
	$body  = $this->Text->autoLinkUrls($body, $target);
	$body  = nl2br($body);
	?>
	</div>

	<div class="panel panel-success">
		<div class="panel-heading"><?= $title; ?></div>
		<div class="panel-body">
			<div class="text-right"><?= $date; ?></div>
			<?= $body; ?>
		</div>
	</div>
</div>