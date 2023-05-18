$(document).ready(function()
{
	$('.text-question').focus();

	$(".text-question").keydown(function(e){
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
	});

	$('.list-group').pagination({
		displayItemCount : 10,
		itemElement : '> .list-group-item',
		paginationInnerClassName : 'pagination',
		prevNextPageBtnMode : false,
		bothEndsBtnHideDisplay : false,
		currentPageNumberClassName : 'disabled',
	});
});

// APIに送信
function sendToAPI()
{
	if($('.text-question').val() == '')
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

function changeTemplate(template_id)
{
	$('#hidTemplateId').val(template_id);
	$('.template-form').submit();
}

function startChat()
{
	$('#hidTemplateId').val(template_id);
	$('.template-form').submit();
}

function getQuestion()
{
	let question = $('.text-question').val();
	
	if(($('.msg').length == 0) && $('.before-body').text() != '')
	{
		question = $('.before-body').text() + '\n' + question;
	}

	if(($('.msg').length == 0) && $('.after-body').text() != '')
	{
		question +=  '\n' + $('.after-body').text();
	}
	
	return question;
}

function deleteChat(chat_key)
{
	if(confirm('削除してもよろしいですか？'))
	{
		location.href = CHAT_DELETE_URL + '/' +  chat_key;
	}

	event.stopPropagation();
	return false;
}

