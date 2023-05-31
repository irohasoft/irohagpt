$(document).ready(function()
{
	// プロンプトにフォーカスをセット
	$('.text-question').focus();
	
	// 改行時にプロンプトを送信
	$(".text-question").keydown(function(e)
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

	// 回答に markdown を適用
	$('.msg-assistant').each(function(index)
	{
		var html = marked.parse($(this).text(),
		{
			breaks: true,
			sanitize: true
		});
		
		$(this).html(html);
	});

	// プロンプトの初期値が存在する場合、APIに送信
	if(
		($('.text-question').length > 0) &&
		($('.text-question').val() != '')
	)
	{
		sendToAPI();
	}
});

// APIに送信
function sendToAPI()
{
	if($('.text-question').val() == '')
	{
		alert('質問が入力されていません');
		return;
	}

	// ローディング画面を表示
	showLoadingScreen();
	
	const question = getQuestion(); // 新しい質問
	const messages = getMessages(); // 対話の履歴
	
	messages.push({role: 'user', content: question});
	
	const data = new FormData();

	data.append('chat_key', CHAT_KEY);
	data.append('template_id', TEMPLATE_ID);
	data.append('messages', JSON.stringify(messages));

	$('.stage').append(
		'<div class="alert alert-success msg msg-user">' + 
		CU.getBrHTML(question) +
		'</div>'
	);

	$('.btn-primary').prop('disabled', true);
	$('.text-question').prop('disabled', true);

	$.ajax({
		data: data,
		type: 'POST',
		url: API_URL,
		cache: false,
		contentType: false,
		processData: false,
		success: function(json) {
			var data = JSON.parse(json);
			
			if(data.httpcode == 200)
			{
				var text = marked.parse(data['message'], {
					breaks: true,
					sanitize: true
				});

				$('.stage').append(
					'<div class="alert alert-warning msg msg-assistant">' + 
					text +
					'</div>' +
					'<div class="elapsed-time">' + data['elapsed_time'] + '秒</div>'
				);
			}
			else
			{
				$('.stage').append(
					'<div class="alert alert-danger">' + 
					data.error.message +
					'</div>'
				);
			}

			$('.btn-primary').prop('disabled', false);
			$('.text-question').prop('disabled', false);
			$('.text-question').val('');
			hideLoadingScreen();
			
			setTimeout("$('.text-question').focus();", 500);

			// チャットのタイトルを更新
			updateTitle();
		},
		error: function(url) {
			alert('通信中にエラーが発生しました');
			$('.btn-primary').prop('disabled', false);
			$('.text-question').prop('disabled', false);
			setTimeout("$('.text-question').focus();", 500);
			hideLoadingScreen();
		}
	});

	return false;
}

// APIに「続けてください」を送信
function continueToChat()
{
	$('.text-question').val('続けてください');
	sendToAPI();
}

// チャットのタイトルを更新
function updateTitle()
{
	const messages = getMessages();
	const data = new FormData();
	
	messages.push({role: 'user', content: '20文字以内で今回の対話のタイトルを考えてください。'})

	data.append('chat_key', CHAT_KEY);
	data.append('messages', JSON.stringify(messages));
	
	$.ajax({
		data: data,
		type: 'POST',
		url: UPDATE_API_URL,
		cache: false,
		contentType: false,
		processData: false,
		success: function(json) {
			console.log('update:' + json);
		},
		error: function(url) {
			console.log('title update error');
		}
	});

	return false;
}

// プロンプトを取得
function getQuestion()
{
	// 質問
	let question = $('.text-question').val();
	
	if(($('.msg').length == 0) && $('.before-body').text() != '')
	{
		question = $('.before-body').text() + '\n\n' + question;
	}

	if(($('.msg').length == 0) && $('.after-body').text() != '')
	{
		question +=  '\n\n' + $('.before-body').text();
	}
	
	return question;
}

// 過去のチャットの履歴を取得
function getMessages()
{
	var messages = [];

	// 過去の対話
	$('.msg').each(function(index)
	{
		if($(this).hasClass('msg-user'))
		{
			messages.push({role: 'user', content: $(this).text()});
			//messages += 'user##SEPA##' + $(this).text() + '##BLOCK##';
		}
		else
		{
			messages.push({role: 'assistant', content: $(this).text()});
			//messages += 'assistant##SEPA##' + $(this).text() + '##BLOCK##';
		}
	});

	return messages;
}

// ローディング画面を表示
function showLoadingScreen()
{
	$('#loading').css('display', 'flex');
}

// ローディング画面を非表示
function hideLoadingScreen()
{
	$('#loading').css('display', 'none');
}
