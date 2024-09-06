$(document).ready(function()
{
	$('.text-question').focus();

	$('.text-question').keydown(textOnKeydown);
	$('.control-text').keydown(textOnKeydown);

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
		alert('質問が入力されていません');
		return;
	}

	// selectをテキストに置換
	CommonUtil.replaceSelectElements('.control-select');
	CommonUtil.replaceTextElements('.control-text');

	$('.text-question').val(getQuestion());
	$('.template-form')[0].action = MESSAGE_URL + CommonUtil.getRandomString(8);
	$('.template-form').submit(); // フォームをPOSTする
}

/**
 * テンプレートを変更し、フォームを送信
 * @param {string} template_id - 選択されたテンプレートのID
 */
function changeTemplate(template_id)
{
	$('#hidTemplateId').val(template_id);  // 隠しフィールドにテンプレートIDをセット
	$('.template-form').submit();  // フォームを送信
}

/**
 * チャットを開始
 * 注意: template_id変数がグローバルスコープで定義されていることを前提としています
 */
function startChat()
{
	$('#hidTemplateId').val(template_id);  // 隠しフィールドにテンプレートIDをセット
	$('.template-form').submit();  // フォームを送信
}

/**
 * 質問テキストを取得し、必要に応じて前後のテキストを追加
 * @returns {string} 整形された質問テキスト
 */
function getQuestion()
{
	let question = $('.text-question').val();  // 質問テキストを取得
    
	// メッセージがなく、前文がある場合、質問の前に追加
	if(($('.msg').length == 0) && $('.before-body').text() != '')
	{
		question = $('.before-body').text() + '\n' + question;
	}

	// メッセージがなく、後文がある場合、質問の後に追加
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

