<?php
/**
 * iroha GPT Project
 *
 * @author        Kotaro Miura
 * @copyright     2015-2021 iroha Soft, Inc. (https://irohasoft.jp)
 * @link          https://docs.irohagpt.com
 * @license       https://www.gnu.org/licenses/gpl-3.0.en.html GPL License
 */

App::uses('AppController', 'Controller');

/**
 * Homes Controller
 * https://book.cakephp.org/2/ja/controllers.html
 */
class HomesController extends AppController
{
	/**
	 * テンプレート一覧（ホーム画面）を表示
	 */
	public function index()
	{
		$user_id = $this->readAuthUser('id');
		$template_id = $this->getData('template_id');
		//debug($template_id);
		
		// 全体のお知らせの取得
		$data = $this->Setting->find()
			->where(['Setting.setting_key' => 'information'])
			->first();
		
		$info = $data['Setting']['setting_value'];
		
		// お知らせ一覧を取得
		$infos = $this->fetchTable('Info')->getInfos($user_id, 2);
		
		$no_info = '';
		
		// 全体のお知らせもお知らせも存在しない場合
		if(($info == '') && (count($infos) == 0))
			$no_info = __('お知らせはありません');
		
		$no_record = '';
		
		$templates = $this->fetchTable('Template')->find()
			->where(['Template.user_id' => $user_id])
			->order('Template.sort_no asc')
			->all();
		/*
		$chats = $this->fetchTable('Message')->find('all', array(
			'fields' => ['chat_key', 'created', "LEFT(GROUP_CONCAT(message SEPARATOR ','), 20) AS message"],
			'group' => 'chat_key',
			'order' => 'Message.id'
		));
		*/
		//debug($chats);
		$template = $this->fetchTable('Template')->find()
			->where(['Template.user_id' => $user_id, 'Template.id' => $template_id])
			->first();
		//debug($template);
		
		$popular_templates = $this->fetchTable('Template')->getPopularTemplates($user_id);
		//debug($popular_templates);

		$chats = $this->fetchTable('Chat')->find()
			->where(['Chat.user_id' => $user_id])
			->order(['Chat.modified desc'])
			->all();

		if(count($templates) == 0)
			$no_record = __('表示可能なテンプレートはありません');
		
		$this->set(compact('templates', 'template', 'template_id', 'popular_templates', 'chats', 'no_record', 'info', 'infos', 'no_info'));
	}

	/**
	 * 送信された画像を保存
	 *
	 * @return string アップロードした画像のURL(JSON形式)
	 */
	public function upload_image()
	{
		$this->autoRender = FALSE;
		
		if($this->request->is('ajax'))
		{
			App::import ('Vendor', 'FileUpload');
			$fileUpload = new FileUpload();
			
			// アップロード可能な拡張子とファイルサイズを指定
			$upload_extensions = (array)Configure::read('upload_image_extensions');
			$upload_maxsize = Configure::read('upload_image_maxsize');
			
			$fileUpload->setExtension($upload_extensions);
			$fileUpload->setMaxSize($upload_maxsize);
			$fileUpload->readFile($this->getParam('form')['file']); // ファイルの読み込み
			
			$str = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 4);
			
			// ファイル名：YYYYMMDDHHNNSS形式＋ランダムな4桁の文字列＋"既存の拡張子"
			$new_name = date('YmdHis').$str.$fileUpload->getExtension($fileUpload->getFileName());

			$file_name = WWW_ROOT.'uploads'.DS.$new_name; // ファイルのパス
			$file_url = Router::url('/uploads/'.$new_name, true); // ファイルのURL (https://xxxx 形式)

			$result = $fileUpload->saveFile($file_name); // ファイルの保存
			
			if ($result) {
				// 画像のリサイズ処理
				$this->resizeImage($file_name, 1024, 1024);
				
				$response = ['imageUrl' => $file_url];
			} else {
				$response = ['error' => true];
			}
			
			// 画像のURLをJSON形式で出力
			return json_encode($response);
		}

		return 'not found';
	}

	/**
	 * 画像をリサイズする
	 *
	 * @param string $file_path 画像ファイルのパス
	 * @param int $max_width 最大幅
	 * @param int $max_height 最大高さ
	 */
	private function resizeImage($file_path, $max_width, $max_height) {
		list($width, $height, $type) = getimagesize($file_path);
		
		// アスペクト比を計算
		$aspect_ratio = $width / $height;

		if ($width > $max_width || $height > $max_height) {
			if ($max_width / $max_height > $aspect_ratio) {
				$new_width = $max_height * $aspect_ratio;
				$new_height = $max_height;
			} else {
				$new_width = $max_width;
				$new_height = $max_width / $aspect_ratio;
			}

			$new_image = imagecreatetruecolor($new_width, $new_height);

			switch ($type) {
				case IMAGETYPE_JPEG:
					$source = imagecreatefromjpeg($file_path);
					break;
				case IMAGETYPE_PNG:
					$source = imagecreatefrompng($file_path);
					break;
				case IMAGETYPE_GIF:
					$source = imagecreatefromgif($file_path);
					break;
			}

			imagecopyresampled($new_image, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

			switch ($type) {
				case IMAGETYPE_JPEG:
					imagejpeg($new_image, $file_path, 90);
					break;
				case IMAGETYPE_PNG:
					imagepng($new_image, $file_path);
					break;
				case IMAGETYPE_GIF:
					imagegif($new_image, $file_path);
					break;
			}

			imagedestroy($new_image);
			imagedestroy($source);
		}
	}
}
