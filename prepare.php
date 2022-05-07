<?php
Logger::set_name('prepare');
Wpails\Router::init();
Wpails\Router::render_404_if_route_doesnt_exist();
Wpails\Router::redirect_if_current_route_has_redirect();
Wpails\Router::render_404_if_not_permitted();
status_header(200);// NOTE: ここまで来たらステータスを200にする(後で変更可能)
Logger::set_name('models_read');
require \file_path::models('main');
Logger::set_name('request');
require \file_path::base('lib/request.php');// NOTE: modelsに依存しているためこの順番
Wpails\Request::init();
$WPAILS_REQUEST = Wpails\Request::now();
$WPAILS_REQUEST->log();
if(!!$WPAILS_REQUEST->module){
  $WPAILS_MODULE = $WPAILS_REQUEST->module->name;
}else{
  $WPAILS_MODULE = null;
}
/**
 * ログインユーザーを取得する関数
 * 
 * ログインしていなければnull
 * $current_userはWPに使われているため代わりに関数を使う
 */
function current_user(){
  global $WPAILS_REQUEST;
  return $WPAILS_REQUEST->user;
}
$LOADED_FILES = [
  'controller' => \file_path::controllers($WPAILS_REQUEST->route->file_path),
  'template' => \file_path::views($WPAILS_REQUEST->route->file_path),
];
if(!!$WPAILS_MODULE){
  $LOADED_FILES = array_merge($LOADED_FILES, [
    'module_helper' => \file_path::helpers('modules/' . $WPAILS_MODULE),
    'module_views_helper' => \file_path::views($WPAILS_MODULE . '/_helpers'),
    'module_template' => \file_path::views($WPAILS_MODULE . '/index'),
    'module_head' => \file_path::views($WPAILS_MODULE . '/_layouts/_head'),
  ]);
}else{// NOTE: Undefined indexが出ないようにするためにnullをセット
  $LOADED_FILES = array_merge($LOADED_FILES, [
    'module_helper' => null,
    'module_views_helper' => null,
    'module_template' => null,
    'module_head' => null,
  ]);
}
Logger::set_name('module_helper');
if(file_exists($LOADED_FILES['module_helper'])){
  require $LOADED_FILES['module_helper'];
}
