SET FOREIGN_KEY_CHECKS=0;

SET FOREIGN_KEY_CHECKS=1;

# 管理者アカウントのパスワードの復旧方法
# 1. UPDATE文の前の#を削除し、「復旧したい管理者のログインID」と「パスワード」を対象のものに置換します。
# 2. ファイルを保存後、ブラウザで /update を実行します。
# 3. #を元の状態に戻し、ファイルを保存します。
#UPDATE ib_users SET `password` = SHA1(CONCAT('%salt%', '新しいパスワード')) WHERE username = '復旧したい管理者のログインID';

