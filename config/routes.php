<?php
namespace Wpails\Config;
$pattern = \file_path::config('routes/*');
foreach(glob($pattern) as $f){
  require $f;
}
function routes() :array{// NOTE: 他のconfigはconstで定義したがroutes()は値を計算して返すことも想定される
  global
    $calendar_routes,
    $controlpael_routes,
    $email_routes;
  return [
    ['slug'=>'', 'title'=>'Home', 'redirect'=>'/dashboard'],
    ['slug'=>'developer', 'is_namespace'=>true,
      'ancestors'=>[
        ['slug'=>'routes', 'title'=>'Routes'],
      ]
    ], [
      'slug'=>'auth', 'is_namespace'=>true, 'no_nested_url'=>true,
      'ancestors'=>[
        ['slug'=>'login', 'title'=>'ログインページ'],
        ['slug'=>'logout', 'no_view'=>true],
        ['slug'=>'forgot_password', 'title'=>'パスワードを忘れた方へ'],
        ['slug'=>'registration', 'title'=>'ユーザー登録'],
        ['slug'=>'reset_password', 'title'=>'パスワードをリセット'],
      ]
    ], [
      'slug'=>'calendar', 'is_namespace'=>true, 'filter'=>'logged_in',
      'ancestors'=>$calendar_routes
    ], [
      'slug'=>'controlpanel', 'is_namespace'=>true, 'no_nested_url'=>true, 'filter'=>'logged_in',
      'ancestors'=>$controlpael_routes
    ], [
      'slug'=>'email', 'is_namespace'=>true, 'filter'=>'logged_in',
      'ancestors'=>$email_routes
    ]
  ];
}
