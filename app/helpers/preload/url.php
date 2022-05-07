<?php
/**
 * URLをモジュールごとに適切に返すURLオブジェクト
 * 
 * モジュールについてはREADME.mdを参照
 * no_nested_urlオプションを切り替えたときにこのファイルを修正するだけですむように、
 * モジュール名と同じ関数名を登録している
 * 
 * @package Helpers
 * @see README.md
 */
class URL{
  /**
   * 本質的な情報を引数としてURLを返却する
   * 
   * パラメータを&でつなげるのは自動化されるべき
   * 
   * @param string $module モジュール名
   * @param string $url クエリを含まないパス
   * @param array $params パラメータの連想配列
   */
  private static function parse(?string $module, string $url, array $params = []) :string{
    $q = '';
    if(!empty($params)){
      $queries = [];
      foreach($params as $key => $value){
        array_push($queries, $key . '=' . $value);
      }
      $q = '?' . implode('&', $queries);
    }
    $url = preg_replace('/\?\.+/', '', $url);
    if($module){
      return home_url('/' . $module . '/' . $url . $q);
    }else{
      return home_url('/' . $url . $q);
    }
  }
  /** 特にモジュールを使わない */
  static function base(string $url, array $params = []) :string{
    return self::parse(null, $url, $params);
  }
  /** 以下はモジュールごと */
  static function hello(string $url, array $params = []) :string{
    return self::parse('hello', $url, $params);
  }
}
