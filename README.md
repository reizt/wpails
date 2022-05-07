# WPAILS マニュアル
### CLANEのWebアプリ開発を支える基幹機能を網羅したWordPressテーマ。
## 基本概念
---
### **MVCフレームワークを採用**
- Models: 投稿とユーザーをオブジェクトとしてラップする
- Views: 表示するHTMLを記述
- Controllers: クエリを処理してmodelsに命令・viewsに変数を渡す
- helpers: 再利用する関数の定義
- config: ルーティングなどの設定ファイル
### **ルーティングに依存した自動的なファイル決定**  
*config/WPAILS_APP_NAME.php*で定義したルーティングの連想配列を元にページ構造が決定される。  
> */controlpanel/project/new*にアクセス  
> → *views/controlpanel/project/new.php*がテンプレートファイルとして使われ、*controllers/controlpanel/project/new.php*がテンプレートファイルの前に呼び出される  

### **独立したモジュール**  
WPAILSは独立した汎用的な機能を複数持つ(モジュールと呼ぶ)。ルーティングは完全に分割されるため、ファイル構造も分割される。
>例えばControlpanelモジュールに固有なファイルはcontrolpanelディレクトリにすべて格納される。
```
assets/
  js/
    controlpanel.js モジュール固有のJSファイル
  scss/
    modules/
      controlpanel/
        モジュール固有のSCSSファイル
    controlpanel.scss モジュール固有のSCSSエントリポイント
controllers/
  controlpanel/
    モジュール固有のPHPファイル
views/
  controlpanel/
    モジュール固有のPHPファイル
```
### **ルーティングへの許可権設定**
ルーティングにフィルターを指定すると、その下のURLにもフィルターが適応される。

フィルターは許可権のリストを持っており、ログインユーザー(または非ログインユーザー)の許可権がリストに含まれるかを検証する。
### **WPのheader.php, footer.phpを使用しない**
特定のファイルはfunctions.phpで読まれるが、ほとんどのファイルはテーマディレクトリ直下のindex.phpを起点にして読み込まれる。
### **WP_Post, WP_Userへの操作をラップしたオブジェクト**
投稿はPost, ユーザーはUserクラスとしてラップする。WordPressは参照に特化した関数が多く用意されているが、更新・追加・削除には少々難を要する。そういった面倒な処理を、極力単純な入力で期待した結果を得られるオブジェクトのメソッドとして隠蔽する。wp_postsとwp_usersとの関数の使い方の違いという面倒なことから解放されることができ、同じ命令で同じ形式の出力が得られる。また、カスタム投稿タイプについてはPostクラスを継承したクラス、ユーザーに関してはuser_metaのuser_typeによって

# 使用方法
### **モジュール追加方法**
  - controlpanelモジュールを追加
    1. 設定ファイル *config/app.php*
       ```PHP
       function routes(){
         return [
           // URLを/controlpanel以下にする場合
           ['slug'=>'controlpanel', 'is_namespace'=>true,
            'ancestors'=>[
              // /controlpanel配下のルーティング
            ]
           ],
           // URLを/controlpanel以下にしない場合
           ['slug'=>'controlpanel', 'is_namespace'=>true, 'no_nested_url'=>true,
            'ancestors'=>[
              // /controlpanel配下のルーティング
            ]
           ],
         ];
       }
       ```
    2. *views/controlpanel*ディレクトリを作成
    3. *controllers/controlpanel*ディレクトリを作成
    4. 以下は任意
       1. *views/controlpanel/index.php*を作成
       2. *controllers/controlpanel/_helpers.php*を作成
       3. *views/controlpanel/_helpers.php*を作成
       4. *helpers/modules/controlpanel.php*を作成
### **ページ追加方法**
  - /controlpanel/projectsを追加
    1. 設定ファイル *config/app.php*
       ```PHP
       /**
        * [
        *   [
        *     'slug'=>スラッグ,
        *     'title'=>タイトル,
        *     'is_namespace'=>名前空間かどうか(ルートが生成されない),
        *     'filter'=>フィルター名
        *     'redirect'=>リダイレクト先(ex: '/dashboard')
        *     'no_view'=>true ビューファイルを表示しない(controllerのみ実行される)
        *     'no_nested_url'=>true ancestorsのルーティングにスラッグを含めない
        *     'ancestors'=>[
        *       同じ形式の連想配列, ...
        *     ]
        *   ], ...
        * ]
       */
       function routes(){
         return [
           ['slug'=>'controlpanel', 'is_namespace'=>true,
            'ancestors'=>[
              ['slug'=>'projects', 'title'=>'プロジェクト管理']
            ]
           ],
         ];
       }
       ```
    2. views/controlpanel/にprojects.phpを作成
    3. **任意:** controllers/controlpanel/にprojects.phpを作成
### **投稿タイプ追加方法**
  - projectを追加
    1. 設定ファイル *config/app.php*
      ```PHP
      function post_types(){
        return [
          ['name' => 'project', 'label' => 'プロジェクト', 'menu_icon'=>'dashicons-clipboard'],
        ];
      }
      ```
    2. models/wp_records/post_ancestors/にproject.phpを追加
      ```PHP
      final class Project extends Post{
        // post_type
        protected const POST_TYPE = 'project';
        /**
        * PostクラスのDEFAULT_COLUMNSにマージするカラム
        *[
        *   [
        *     'name'=> DBに保存するカラム名
        *     'label'=> 表示するカラム名
        *     'validations'=>[ バリデーション
        *       'required'=> 必須項目か
        *     ]
        *   ], ...
        * ]
        */
        protected const ORIGINAL_COLUMNS = [
          ['name'=>'post_title', 'label'=>'プロジェクト名', 'validations'=>['required'=>true]],
        ];
        /**
        * selectタグなどで使う静的な選択肢
        * [
        *   DBに保存されるカラム名 => [
        *    [
        *    'value'=>DBに保存する値,
        *    'label'=>optionタグに表示される値
        *    ], ...
        * ]
        */
        const STATIC_OPTIONS = [
          'billing_status' => [
            ['value'=>'unclaimed', 'label'=>'未請求'],
            ['value'=>'billed', 'label'=>'請求済み'],
            ['value'=>'deposited', 'label'=>'入金済み'],
          ],
        ];
      }
      ```
### **ルーティングへの許可権設定**
1. Userの子クラスを参照 *models/wp_records/user_ancestors/\*.php*
  ```PHP
  class Employee extends User{
    // 省略
    const STATIC_OPTIONS = [
      'permission'=>[
        ['value' => 'admin', 'label' => '管理者'],
        ['value' => 'staff', 'label' => 'スタッフ'],
      ]
    ]
  }
  ```
  ```PHP
  class Client extends User{
    // 省略
    protected const ORIGINAL_COLUMNS = [
      // 省略
      ['name'=>'permission', 'label'=>'アクセス権限', 'default'=>'client'],
    ];
    // 省略
  }
  ```
  この場合使える許可権名はadmin, staff, client, unknown(非ログインユーザー)  
2. 設定ファイルに記述 *config/app.php*
  ```PHP
  /**
   * [
   *   フィルター名 => [
   *    許可する許可権名, ...
  *   ],
  * ]
  */
  function filters(){
    return [
      'logged_in'=>[
        'admin', 'staff', 'client',
      ],
      'only_admin'=>[
        'admin'
      ],
    ]
  }
  ```
  この場合logged_inフィルターはpermissionカラムがadmin, staff, clientであるユーザーのみ許可する
# 開発環境の構築 (Ubuntu)
1. PHPのインストール
   1. aptのアップデート `sudo apt update -y`
   2. PHPのインストール `sudo apt install php libapache2-mod-php php-gd php-xml php-cli php-mbstring php-soap php-xmlrpc php-zip`
   3. インストールされているか確認 `php -v`
2. Composerのインストール
   1. composer.pharが現在のディレクトリに作成される `curl -sS https://getcomposer.org/installer | php`
   2. PATHが通っている場所に移動する `sudo mv composer.phar /usr/local/bin/composer`
   3. 実行権限を与える `sudo chmod +x /usr/local/bin/composer`
   4. シェルをリロード `source ~/.bashrc`
   5. インストールされているか確認 `composer -v`
3. PHP Carbonのインストール
   1. carbonをプロジェクトに追加 `composer require nesbot/carbon`
4. PHP Monologのインストール
   1. monologをプロジェクトに追加 `composer require monolog/monolog`