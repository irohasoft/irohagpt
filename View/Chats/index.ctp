<div class="panel panel-info">
    <div class="panel-heading"><?= __('チャット検索'); ?></div>
    <div class="panel-body">
        <?= $this->Form->create(null, ['type' => 'get']); ?>
        <div class="search-box mb-3">
            <div class="input-group">
                <input name="keyword" class="form-control" placeholder="<?= __('チャットを検索'); ?>" type="text" value="<?= h($keyword); ?>" id="ChatsKeyword">
                <span class="input-group-btn">
                    <?= $this->Form->button(__('検索'), [
                        'class' => 'btn btn-default'
                    ]); ?>
                </span>
            </div>
        </div>
        <?= $this->Form->end(); ?>

        <ul class="list-group">
            <?php if (count($chats) > 0): ?>
                <?php foreach ($chats as $chat): ?>
                    <a href="#" class="list-group-item" onclick="location.href='<?= Router::url(['controller' => 'messages', 'action' => 'index', $chat['Chat']['chat_key']]);?>'">
                        <button type="button" class="btn btn-danger pull-right" onclick="deleteChat('<?= $chat['Chat']['chat_key']?>');">削除</button>
                        <div class="list-group-item-heading display-3"><b><?= h($chat['Chat']['title']);?></b></div>
                        <p class="list-group-item-text">
                            <span class="first-date"><?= __('作成').': '.Utils::getYMD($chat['Chat']['created']); ?></span>
                            <span class="last-date"><?= __('更新').': '.Utils::getYMD($chat['Chat']['modified']); ?></span>
                        </p>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center">
                    <div class="alert alert-warning">
                        <?= __('検索結果がありません'); ?>
                    </div>
                </p>
            <?php endif; ?>
        </ul>
        <?= $this->element('paging');?>
        <div class="text-right">
            <?= $this->Html->link(
                __('戻る'),
                ['controller' => 'homes', 'action' => 'index'],
                ['class' => 'btn btn-default']
            ); ?>
        </div>
    </div>
</div>

<?php $this->start('script-embedded'); ?>
<script>
    var CHAT_DELETE_URL = '<?= Router::url(['controller' => 'chats', 'action' => 'delete'])?>';
</script>
<?php $this->end(); ?>
<?= $this->Html->script('homes.js?20241001');?>
