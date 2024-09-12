<div class="drop-container">
    <div class="uploaded-images-container"></div>
    <input type="file" name="data[Content][file]" id="ContentFile" class="form-control" accept="image/*" style="display:none;">
    <input type="file" name="data[Content][file]" id="ContentCamera" class="form-control" accept="image/*" capture="camera" style="display:none;">
    <input type="hidden" id="hidImageUrls" name="image_urls" value="<?= $image_urls?>" />
    <textarea class="form-control text-question" name="message" maxlength="<?= Configure::read('prompt_max') ?>" type="text" required placeholder="質問を入力し、エンターキーを押下してください。
改行する場合は、Shift+エンターキーを押下してください。
ドラックアンドドロップで画像を指定することも可能です。"><?= $message?></textarea>
</div>
<button class="btn btn-default" id="btnSend"><span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span> <?=__('送信')?></button>
<button class="btn btn-default" id="btnImage"><span class="glyphicon glyphicon-picture" aria-hidden="true"></span> <?=__('画像')?></button>
<button class="btn btn-default" id="btnCamera"><span class="glyphicon glyphicon-camera" aria-hidden="true"></span> <?=__('撮影')?></button>