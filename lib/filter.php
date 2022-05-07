<?php
/**
 * \Wpails\Filterクラスのファイル
 * 
 * filterという概念についてはREADME.mdを参照。
 * 
 * @package Built-In
 * @see lib/helpers.php array_find()
 * @see README.md
 */
namespace Wpails;
class Filter{
  /** Filterインスタンスの配列 */
  private static array $instances = [];
  /** 初期化されているか */
  private static bool $is_inited = false;
  function __construct(string $name, array $allow_list = []){
    $this->name = $name;
    $this->allow_list = $allow_list;
  }
  /**
   * 配列の要素をそれぞれFilterオブジェクトに変換してself::$instancesに格納
   * @param array{string => string[]} config/WPAILS_APP_NAME.phpにて設定したフィルターの配列
   */
  static function init(array $filters) :void{
    if(self::$is_inited) throw new \ErrorException('WPAILS Error: Filter is already inited!');
    foreach($filters as $name => $allow_list){
      array_push(self::$instances, new \Wpails\Filter($name, $allow_list));
    }
    self::$is_inited = true;
  }
  /**
   * フィルター名で検索してFilterオブジェクトを返す
   * @param string $name フィルター名
   */
  static function get(?string $name) :?Filter{
    return array_find(
      self::$instances, function($f) use($name){
        return $f->name === $name;
      }
    );
  }
  /**
   * 許可権名を許可しているかを返す
   * @param string $permission 許可権名
   */
  function permits(string $permission) :bool{
    return in_array($permission, $this->allow_list);
  }
}
