/**
 * iroha GPT Project
 *
 * @author        Kotaro Miura
 * @copyright     2015-2021 iroha Soft, Inc. (https://irohasoft.jp)
 * @link          https://docs.irohagpt.com
 * @license       https://www.gnu.org/licenses/gpl-3.0.en.html GPL License
 */
 
$(function (event)
{
	/*
	$('.btn').click(function(event)
	{
		event.preventDefault();
		return false;
	});
	*/
	
	$('.btn-primary').prop('disabled', true);
	$('.btn-score').prop('disabled', false);
	$('.btn-danger').attr("onclick", 'alert("デモモードの為、削除できません");');
	$('.admin-users-edit .btn-default').attr("onclick", 'alert("デモモードの為、削除できません");');
	
	$('.admin-users-index .btn-import').prop('disabled', false); // ユーザインポートボタン
	
	$('.btn-add').prop('disabled', false);
	$('.btn[value="ログイン"]').prop('disabled', false);
	
	if(location.href.indexOf('demoib.irohasoft.com') > 0)
	{
		if(location.href.indexOf('admin') > 0)
		{
			$("#UserUsername").val("root");
			$("#UserPassword").val("irohaboard");
		}
		else
		{
			var day = ((new Date()).getDay()+1);
			
			$("#UserUsername").val("demo00" + day);
			$("#UserPassword").val("pass");
		}
	}
});

