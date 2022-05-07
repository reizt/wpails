<?php
/**
 * \Wpails\Moduleクラスのファイル
 * 
 * moduleという概念についてはREADME.mdを参照。
 * $nowをprivateにしてゲッターのみを公開。
 * さらに$is_initedを一方通行にして一度しか初期化できないようにしている
 * 
 * @package Built-In
 * @see lib/route.php
 * @see README.md
 */
namespace Wpails;
class Module{
  /** 現在のリクエストに対応するモジュール */
  private static ?Module $now;
  /** 初期化されているか */
  private static bool $is_inited = false;
  function __construct(string $name){
    $this->name = $name;
  }
  /** @param string[] $modules モジュールの名前の配列
   *  @param Route $route モジュール情報を抽出する対象のRouteオブジェクト
   *  @return void
   */
  static function init(array $modules, Route $route) :void{
    if(self::$is_inited) throw new ErrorException('WPAILS Error: Module is already inited!');
    $module = $route->module;
    if(in_array($module, $modules)){
      self::$now = new self($module);
    }else{
      self::$now = null;
    }
    self::$is_inited = true;
  }
  /** 現在のモジュールオブジェクトを返す */
  static function now() :?Module{
    return self::$now;
  }
}
