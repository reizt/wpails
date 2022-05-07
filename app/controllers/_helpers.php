<?php
class Flash{
  private static $array = [];
  static function set(string $type, mixed $message) :void{
    $_SESSION['flash'][$type] = $message;
    self::$array[$type] = $message;
    self::$array = isset($_SESSION['flash']) ? $_SESSION['flash'] : [];
    unset($_SESSION['flash']);
  }
  static function get() :array{
    return self::$array;
  }
}
function filtered_array(array $array, array $allow_list) :array{
  $params = [];
  foreach($array as $key => $value){
    if(in_array($key, $allow_list)){
      $params[$key] = $value;
    }elseif(preg_match('/^[^ ]+_trigger$/', $key)){
      $params[$key] = '';// triggerなら通すが値は入れない
    }
  }
  return $params;
}
function filter_GET(array $allow_list) :void{
  $_GET = filtered_array($_GET, $allow_list);
}
function filter_POST(array $allow_list) :void{
  $_POST = filtered_array($_POST, $allow_list);
}
