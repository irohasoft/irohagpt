# iroha Chat
iroha Chat はプロンプトのテンプレートを作成、利用することで、ChatGPTと効率的かつ効果的な対話が実現できるのAIチャット管理システムです。
独自のテンプレートエンジンとシンプルなインターフェイスが特徴で、初心者でも簡単に利用できます。汎用性が高く、教育・学習支援、業務の効率化、プログラム開発支援など、様々な分野で活用ができます。

## 公式サイト
https://docs.irohagpt.com/

## 動作環境
* PHP : 7.4以上
* MySQL : 5.6以上

## インストール方法
1. フレームワークのソースをダウンロードし、解凍します。
https://github.com/cakephp/cakephp/releases/tag/2.10.24
2. iroha Chat のソースをダウンロードし、解凍します。
https://github.com/irohasoft/irohaboard/releases
3. app ディレクトリ内のソースを iroha Chat のソースに差し替えます。
4. データベース(app/Config/database.php)の設定を行います。
   ※事前に空のデータベースを作成する必要があります。(推奨文字コード : UTF-8)
5. OpenAIのシークレットキー(app/Config/openai.php)の設定を行います。
   ※事前にOpenAI社のサイト（https://platform.openai.com/）でAPIキーを作成する必要があります。
6. 公開ディレクトリに全ソースをアップロードします。
7. ブラウザを開き、http://(your-domain-name)/install にてインストールを実行します。

## 主な機能
### 利用者側
* テンプレートの作成機能
* チャット機能
* チャット履歴の表示
* お知らせの表示

### 管理者側
* ユーザ管理
* グループ管理
* お知らせ管理
* システム設定

## License
GPLv3
