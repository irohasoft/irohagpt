SET FOREIGN_KEY_CHECKS=0;

SET FOREIGN_KEY_CHECKS=1;

# 管理者アカウントのパスワードの復旧方法
# 1. UPDATE文の前の#を削除し、「復旧したい管理者のログインID」と「パスワード」を対象のものに置換します。
# 2. ファイルを保存後、ブラウザで /update を実行します。
# 3. #を元の状態に戻し、ファイルを保存します。
#UPDATE ib_users SET `password` = SHA1(CONCAT('%salt%', '新しいパスワード')) WHERE username = '復旧したい管理者のログインID';

UPDATE ib_templates SET is_master = 0 WHERE is_master is null;

ALTER TABLE ib_messages ADD COLUMN image_urls varchar(2000) DEFAULT NULL AFTER message;
ALTER TABLE ib_templates MODIFY COLUMN is_master int(11) NOT NULL DEFAULT 0;


