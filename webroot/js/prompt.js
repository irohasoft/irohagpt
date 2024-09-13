$(document).ready(function()
{
    // メッセージにフォーカスをセット
	$('.text-message').focus();

    // 改行時にメッセージを送信
	$(".text-message").keydown(function(e)
	{
		if(e.keyCode === 13 && !e.shiftKey)
		{ // Enterキーのみの場合はフォームをPOSTする
			e.preventDefault(); // フォームのPOSTをキャンセル
			sendToAPI(); // フォームをPOSTする
		}
		else if(e.keyCode === 13 && e.shiftKey)
		{ // Shift+Enterキーの場合は改行する
			e.preventDefault(); // デフォルトの改行処理をキャンセル
			var start = this.selectionStart;
			var end = this.selectionEnd;
			var value = $(this).val();
			$(this).val(value.substring(0, start) + "\n" + value.substring(end));
			this.selectionStart = this.selectionEnd = start + 1; // キャレットを改行した場所に移動する
		}
	});

    $('#btnSend').on('click', function(event)
    {
        event.preventDefault();   // デフォルトの動作を防止
        sendToAPI(); // フォームをPOSTする
    });

    // 画像ボタンのクリックイベントを追加
    $('#btnImage').on('click', function(e) {
        e.preventDefault();
        $('#ContentFile').click();
    });

    // スマートデバイスの場合、撮影ボタンを表示する
    if(CU.isSmartDevice())
    {
        $('#btnCamera').show();
    }

    // 画像ボタンのクリックイベントを追加
    $('#btnCamera').on('click', function(e) {
        e.preventDefault();
        $('#ContentCamera').click();
    });

    // ドラッグ開始時のイベントハンドラ
    $('.drop-container').on("dragenter", function(event)
    {
        event.stopPropagation();  // イベントの伝播を停止
        event.preventDefault();   // デフォルトの動作を防止
    });
    
    // ドラッグ中のイベントハンドラ
    $('.drop-container').on("dragover", function(event)
    {
        event.stopPropagation();  // イベントの伝播を停止
        event.preventDefault();   // デフォルトの動作を防止
        $('.drop-container').addClass('drag-over');  // ドラッグ中のスタイルを適用
    });
    
    // ドラッグ要素がエリアから出た時のイベントハンドラ
    $('.drop-container').on("dragleave", function(event)
    {
        event.stopPropagation();  // イベントの伝播を停止
        event.preventDefault();   // デフォルトの動作を防止
        $('.drop-container').removeClass('drag-over');  // ドラッグ中のスタイルを解除
    });
    
    // ドロップ時のイベントハンドラ
    $('.drop-container').on("drop", function(event)
    {
        event.stopPropagation();  // イベントの伝播を停止
        event.preventDefault();   // デフォルトの動作を防止

        var files = event.originalEvent.dataTransfer.files;  // ドロップされたファイルを取得

        $("#ContentFile")[0].files = files;  // ファイル入力要素にファイルをセット
        uploadFiles(files);  // ファイルアップロード処理を呼び出し
    });

    // ファイル選択時のイベントハンドラ
    $('#ContentFile').on('change', function(event)
    {
        event.stopPropagation();  // イベントの伝播を停止
        event.preventDefault();   // デフォルトの動作を防止
        
        var files = event.target.files;  // 選択されたファイルを取得

        if(files.length > 0)
            uploadFiles(files);  // ファイルアップロード処理を呼び出し
    });

    // 指定されたファイルをアップロード
    function uploadFiles(files)
    {        
        $('.drop-container').removeClass('drag-over');

        if(files.length == 0)
        {
            alert('このブラウザはファイルのドロップをサポートしておりません。');
            return;
        }
        
        var file = files[0];
        var formData = new FormData();
        formData.append('file', file);

        $.ajax({
            url: IMAGE_UPLOAD_URL,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                var responseObject = JSON.parse(response);

                if(responseObject.error_code == 0)
                {
                    console.log('ファイルが正常にアップロードされました。');
                    console.log(response);

                    var newImageUrl = responseObject.image_url;

                    // 新しい画像を追加
                    addUploadedImage(newImageUrl);
    
                    // hidden項目を更新
                    updateHiddenImageUrls();

                    $('.text-message').focus();
                }
                else
                {
                    alert(responseObject.error_message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('File upload failed:', textStatus, errorThrown);
            }
        });
    }

    // 画像削除ボタンのイベントハンドラ
    $(document).on('click', '.remove-image', function()
    {
        var urlToRemove = $(this).data('url');
        var existingUrls = JSON.parse($('#hidImageUrls').val() || '[]');
        var updatedUrls = existingUrls.filter(function(url) {
            return url !== urlToRemove;
        });
        $('#hidImageUrls').val(JSON.stringify(updatedUrls));
        $(this).closest('.uploaded-image-container').remove();
    });
});

// アップロードされた画像を元にURLリストを更新
function updateHiddenImageUrls()
{
    var imageUrls = [];
    $('.uploaded-image-container img').each(function() {
        imageUrls.push($(this).attr('src'));
    });
    $('#hidImageUrls').val(JSON.stringify(imageUrls));
}

// 新しい画像を追加
function addUploadedImage(newImageUrl)
{
    var imgTag = '<div class="uploaded-image-container">' +
                    '<img src="' + newImageUrl + '" class="uploaded-image">' +
                    '<button type="button" class="btn btn-sm btn-danger remove-image" data-url="' + newImageUrl + '">×</button>' +
                    '</div>';
    $('.uploaded-images-container').append(imgTag);
}