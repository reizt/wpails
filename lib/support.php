<?php
/**
 * PHPの組み込み関数に準じたオリジナル関数を追加
 * 
 * @package Built-In
 */
function var_name(mixed $v) :string{
  $trace = debug_backtrace();
  $file = $trace[2]['file'];
  $line = $trace[2]['line'];
  $vLine = file($file);
  $fLine = $vLine[$line-1];
  preg_match("/\\$\w+/", $fLine, $match);
  $varname = $match[0];
  return $varname;
}
function is_null_or_empty(mixed $o) :bool{
  return $o === null || $o === '';
}
function is_url(string $string) :bool{
  $VALID_URL_REGEX = '/https?:\/{2}[\w\/:%#\$&\?\(\)~\.=\+\-]+/';
  return preg_match($VALID_URL_REGEX, $string);
}
function str_multi_replace(array $replacements, string $subject) :string{
  return str_replace(array_keys($replacements), array_values($replacements), $subject);
}
function array_find(array $array, callable $filter) :mixed{
  $hit = array_filter($array, $filter);
  if(is_array($hit) && count($hit) > 0){
    return array_shift($hit);
  }else{
    return null;
  }
}
function array_map_by_func(callable $func, array $array) :array{
  return array_map(function($i) use($func){return $func($i);}, $array);
}
function array_map_by_member(string $member, array $array) :array{
  return array_map(function($i) use($member){return $i->$member;}, $array);
}
function array_map_by_key(string $key, array $array) :array{
  return array_map(function($i) use($key){return $i[$key];}, $array);
}
/**
 * PostまたはUserの配列の要素それぞれの特定のattributeの配列を返す関数
 * 
 * modelsが読み込まれた後でないと使えないので注意
 * 
 * @param string $attr カラム名
 * @param Post[]|User[] $collection Post, Userもしくはそれらの子クラスのインスタンスの配列
 * @return mixed[]
 */
function array_map_by_attribute(string $attr, array $collection) :array{
  return array_map(function($p) use($attr){
    return $p->attribute($attr);
  }, $collection);
}
function array_reject_null(array $array) :array{
  return array_diff($array, [null]);
}
