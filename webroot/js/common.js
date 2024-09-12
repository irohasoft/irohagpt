/**
 * iroha GPT Project
 *
 * @author        Kotaro Miura
 * @copyright     2015-2021 iroha Soft, Inc. (https://irohasoft.jp)
 * @link          https://docs.irohagpt.com
 * @license       https://www.gnu.org/licenses/gpl-3.0.en.html GPL License
 */


$(document).ready(function()
{
	// 一定時間経過後、メッセージを閉じる
	setTimeout(function() {
		$('#flashMessage').fadeOut("slow");
	}, 1500);
});


function CommonUtility() {}

CommonUtility.prototype.getHHMMSSbySec = function (sec)
{
	var date = new Date('2000/1/1');
	
	date.setSeconds(sec);
	
	var h = date.getHours();
	var m = date.getMinutes();
	var s = date.getSeconds();
	
	if (h < 10)
		h = '0' + h;
	
	if (m < 10)
		m = '0' + m;
	
	if (s < 10)
		s = '0' + s;
	
	var hms = h + ':' + m + ':' + s;
	
	return hms;
}

CommonUtility.prototype.getBrHTML = function (text)
{
	var element = document.createElement('div');
	element.innerText = text;
	var sanitizedHTML = element.innerHTML.replace(/\n/g, "<br>");
	return sanitizedHTML;
}

CommonUtility.prototype.replaceSelectElements = function (selector)
{
	$(selector).each(function()
	{
		const selectedOptionText = $(this).find('option:selected').text();
		$(this).before(selectedOptionText);
		$(this).remove();
	});
}

CommonUtility.prototype.replaceTextElements = function (selector)
{
	$(selector).each(function()
	{
		const text = $(this).val();
		$(this).before(text);
		$(this).remove();
	});
}

CommonUtility.prototype.isSmartDevice = function()
{
	return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
}

CommonUtility.prototype.getRandomString = function (digit)
{
	var chars = '23456789abcdefghijkmnopqrstuvwxyz';
	var randomString = '';
	for (var i = 0; i < digit; i++) {
	randomString += chars[Math.floor(Math.random() * chars.length)];
	}
	return randomString;
}

var CommonUtil = new CommonUtility();
var CU = new CommonUtility();
