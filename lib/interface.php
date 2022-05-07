<?php
/**
 * WPAILSのconfigを取得しやすくするための関数をまとめる
 * 
 * @package Built-In
 */
namespace Wpails;
function is_development() :bool{
  return Config\ENV === 'development';
}
function is_production() :bool{
  return Config\ENV === 'production';
}
/** config/WPAILS_APP_NAME.phpで設定した値を参照する
 * @param string $key DEVELOPMENT, PRODUCTION, PRESETに入っているキー
 * @return mixed
 */
function get_config(string $key) :mixed{
  if(is_development() && isset(Config\DEVELOPMENT[$key])){
    return Config\DEVELOPMENT[$key];
  }elseif(is_production() && isset(Config\PRODUCTION[$key])){
    return Config\PRODUCTION[$key];
  }elseif(isset(Config\PRESET[$key])){
    return Config\PRESET[$key];
  }
}
