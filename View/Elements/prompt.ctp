<div class="uploaded-images-container"></div>
<div class="drop-container alert alert-warning">
    <p><span class="glyphicon glyphicon-picture" aria-hidden="true"></span> <?=__('画像ファイル（ドラックアンドドロップによる指定も可能）')?></p>
    <input type="file" name="data[Content][file]" id="ContentFile" class="form-control" accept="image/*">
    <input type="hidden" id="hidImageUrls" name="image_urls" value="<?= $image_urls?>" />
</div>
<textarea class="form-control text-question" name="message" maxlength="<?= Configure::read('prompt_max') ?>" type="text" required placeholder="質問を入力し、エンターキーを押下してください。
改行する場合は、Shift+エンターキーを押下してください。"><?= $message?></textarea>
