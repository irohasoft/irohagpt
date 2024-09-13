$(document).ready(function()
{	
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

	// メッセージの初期値が存在する場合、APIに送信
	if(
		($('.text-message').length > 0) &&
		($('.text-message').val() != '')
	)
	{
		sendToAPI();
	}
});

// APIに送信
function sendToAPI()
{
	if($('.text-message').val() == '')
	{
		alert('メッセージが入力されていません');
		return;
	}

	// ローディング画面を表示
	showLoadingScreen();
	
	const content = getQuestion(); // 新しいメッセージ
	const messages = getMessages(); // 対話の履歴
	
	messages.push({role: 'user', content: content});
	
	const data = new FormData();

	data.append('chat_key', CHAT_KEY);
	data.append('template_id', TEMPLATE_ID);
	data.append('messages', JSON.stringify(messages));

	var text = '';

	// 画像付きでない場合テキストを表示
	if(typeof content === 'string')
	{
		$('.stage').append(
			'<div class="alert alert-success msg msg-user">' + 
			CU.getBrHTML(content) +
			'</div>'
		);
	}
	else
	{
		$('.stage').append(
			'<div class="alert alert-success msg msg-user">' + 
			CU.getBrHTML(content[0]['text']) +
			'</div>'
		);

		var imgTag = '<div style="text-align:right;">';

		// 画像を追加
		for(var i = 1; i < content.length; i++)
		{
			imgTag += '<div class="uploaded-image-container"><img src="' + content[i]['image_url']['url'] + '" alt="アップロードされた画像" class="uploaded-image">';
		}

		imgTag += '</div>';
		$('.stage').append(imgTag);
	}


	$('.btn-primary').prop('disabled', true);
	$('.text-message').prop('disabled', true);

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
				// ChatGPTの回答を出力
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
				// エラー処理
				
				// トークン数上限オーバーの場合
				if(data.error.code == 'context_length_exceeded')
				{
					$('.stage').append(
						'<div class="alert alert-danger">' + 
						'処理できる文字数の上限を超えました。新しくチャットを作成してください。' + 
						'</div>'
					);
				}
				// APIキーが無効の場合
				else if(data.error.code == 'invalid_api_key')
				{
					$('.stage').append(
						'<div class="alert alert-danger">' + 
						'設定されているAPIキーが無効です。' + 
						'</div>'
					);
				}
				// その他のエラーの場合
				else
				{
					$('.stage').append(
						'<div class="alert alert-danger">' + 
						'ChatGPT API で以下のエラーが発生いたしました。<br>' + 
						'エラーの種類 : ' + data.error.type + '<br>' +
						'エラーコード : ' + data.error.code + '<br>' +
						'エラーの内容 : ' + data.error.message +
						'</div>'
					);
				}
			}

			// メッセージおよび画像の選択をクリアする
			$('.btn-primary').prop('disabled', false);
			$('.text-message').prop('disabled', false);
			$('.text-message').val('');
			$('.uploaded-images-container').empty();
			$('#hidImageUrls').val('[]');
			hideLoadingScreen();
			
			setTimeout("$('.text-message').focus();", 500);

			// チャットのタイトルを更新
			updateTitle();
		},
		error: function(url) {
			alert('通信中にエラーが発生しました');
			$('.btn-primary').prop('disabled', false);
			$('.text-message').prop('disabled', false);
			setTimeout("$('.text-message').focus();", 500);
			hideLoadingScreen();
		}
	});

	return false;
}

// APIに「続けてください」を送信
function continueToChat()
{
	$('.text-message').val('続けてください');
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

// メッセージを取得
function getQuestion()
{
	// メッセージ
	let content = $('.text-message').val();

	if(($('.msg').length == 0) && $('.before-body').text() != '')
	{
		content = $('.before-body').text() + '\n\n' + content;
	}

	if(($('.msg').length == 0) && $('.after-body').text() != '')
	{
		content +=  '\n\n' + $('.before-body').text();
	}
	
	if($('#hidImageUrls').val())
	{
		let imageUrls = JSON.parse($('#hidImageUrls').val());
		content = [
			{
				type: 'text',
				text: content
			}
		];
		
		imageUrls.forEach(url => {
			content.push({
				type: 'image_url',
				image_url: {
					url: url
				}
			});
		});
	}

	return content;
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
			var content = $(this).text();
			var imageUrls = $(this).data('image-urls'); // 画像URLを取得

			if (imageUrls) {
				// 画像URLが存在する場合、新しい形式でメッセージを追加
				messages.push({
					role: 'user',
					content: [
						{
							type: 'text',
							text: content
						},
						{
							type: 'image_url',
							image_url: {
								url: imageUrl
							}
						}
					]
				});
			} else {
				// 画像URLが存在しない場合、通常のテキストメッセージとして追加
				messages.push({role: 'user', content: content});
			}
		} else {
			messages.push({role: 'assistant', content: $(this).text()});
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
