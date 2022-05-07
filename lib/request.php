<?php
/**
 * \Wpails\Requestクラスのファイル
 * 
 * リクエストの情報をもつオブジェクト
 * $nowをprivateにしてゲッターのみを公開。
 * さらに$is_initedを一方通行にして一度しか初期化できないようにしている
 * 
 * @package Built-In
 */
namespace Wpails;
class Request{
  /** @var Request 現在のリクエスト */
  private static $now;
  /** @var bool 初期化されているか */
  private static $is_inited = false;
  /**
   * インスタンスを1つ作成
   */
  static function init() :void{
    if(self::$is_inited) throw new \ErrorException('WPAILS Error: Request is already inited!');
    $route = \Wpails\Route::now();
    $module = \Wpails\Module::now();
    $user_id = get_current_user_id();
    if(!!$user_id){
      $meta = get_user_meta($user_id);
      $user_type = @$meta['user_type'][0];
      if($user_type === 'employee'){
        $user = \Employee::find($user_id);
      }else{
        $user = \Client::find($user_id);
      }
    }else{
      $user = null;
    }
    self::$now = new self(
      $route, $module, $user,
    );
    self::$is_inited = true;
  }
  /** self::$nowのゲッター */
  static function now() :Request{
    return self::$now;
  }
  /**
   * @param \Wpails\Route $route
   * @param \Wpails\Module $module
   * @param User|null $user
   */
  function __construct($route, $module, $user){
    $this->route = $route;
    $this->module = $module;
    $this->user = $user;
  }
  /**
   * リクエスト情報を出力
   */
  function log() :void{
    $log = $_SERVER['REQUEST_METHOD'] . '  ' . $this->route->path;
    if(!!$this->user){
      $log .= ' as ' . $this->user->attribute('display_name')
              . ' (permission: ' . $this->user->attribute('permission')
              . '/ role: ' . $this->user->attribute('role')
              . ')';
    }
    \Logger::info($log);
  }
  /**
   * _triggerを省略してPOSTに含まれるか確かめられる
   * 
   * form_trigger_tagと連携して_triggerを暗黙でつけられる仕組み
   * @param string $trigger_name
   * @return bool
   * @see views/_helpers.php
   */
  function is_POST_from(string $trigger_name) :bool{
    return isset($_POST[$trigger_name . '_trigger']);
  }
}
