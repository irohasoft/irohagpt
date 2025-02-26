$(document).ready(function()
{
	$('.list-group').pagination({
		displayItemCount : 10,
		itemElement : '> .list-group-item',
		paginationInnerClassName : 'pagination',
		prevNextPageBtnMode : false,
		bothEndsBtnHideDisplay : false,
		currentPageNumberClassName : 'disabled',
	});
});

function textOnKeydown(e)
{
	if (e.keyCode === 13 && !e.shiftKey) { // Enterキーのみの場合はフォームをPOSTする
		e.preventDefault(); // フォームのPOSTをキャンセル
		sendToAPI(); // フォームをPOSTする
	} else if (e.keyCode === 13 && e.shiftKey) { // Shift+Enterキーの場合は改行する
		e.preventDefault(); // デフォルトの改行処理をキャンセル
		var start = this.selectionStart;
		var end = this.selectionEnd;
		var value = $(this).val();
		$(this).val(value.substring(0, start) + "\n" + value.substring(end));
		this.selectionStart = this.selectionEnd = start + 1; // キャレットを改行した場所に移動する
	}
}

// APIに送信
function sendToAPI()
{
	if(getQuestion() == '')
	{
		alert('メッセージが入力されていません');
		$('.text-message').focus();
		return;
	}

	// selectをテキストに置換
	CommonUtil.replaceSelectElements('.control-select');
	CommonUtil.replaceTextElements('.control-text');

	$('.text-message').val(getQuestion());
	$('.template-form')[0].action = MESSAGE_URL + CommonUtil.getRandomString(8);
	$('.template-form').submit(); // フォームをPOSTする
}

/**
 * テンプレートを変更し、フォームを送信
 */
function changeTemplate(template_id)
{
	$('#hidTemplateId').val(template_id);  // 隠しフィールドにテンプレートIDをセット
	$('.template-form').submit();  // フォームを送信
}

/**
 * チャットを開始
 */
function startChat(template_id)
{
	$('#hidTemplateId').val(template_id);  // 隠しフィールドにテンプレートIDをセット
	$('.template-form').submit();  // フォームを送信
}

/**
 * メッセージテキストを取得し、必要に応じて前後のテキストを追加
 * @returns {string} 整形されたメッセージテキスト
 */
function getQuestion()
{
	let question = $('.text-message').val();  // メッセージテキストを取得
    
	// メッセージがなく、前文がある場合、メッセージの前に追加
	if(($('.msg').length == 0) && $('.before-body').text() != '')
	{
		question = $('.before-body').text() + '\n' + question;
	}

	// メッセージがなく、後文がある場合、メッセージの後に追加
	if(($('.msg').length == 0) && $('.after-body').text() != '')
	{
		question +=  '\n' + $('.after-body').text();
	}
	
	return question;
}

/**
 * チャットを削除
 * @param {string} chat_key - 削除するチャットのキー
 * @returns {boolean} false - イベントのデフォルト動作を防ぐため
 */
function deleteChat(chat_key)
{
	if(confirm('削除してもよろしいですか？'))  // 削除確認ダイアログを表示
	{
		location.href = CHAT_DELETE_URL + '/' +  chat_key;  // 削除URLに遷移
	}

	event.stopPropagation();  // イベントの伝播を停止
	return false;  // デフォルトの動作を防止
}

