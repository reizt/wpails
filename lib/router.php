<?php
/**
 * Route, Module, Filterを使ってルーティングを制御する\Wpails\Routerオブジェクト
 * 
 * ルートが存在するか検証
 * リダイレクトオプションがあるか検証
 * リクエストしたユーザーが許可されているか検証
 * 処理の途中で404ページなどを返す
 * controllerが終了したらdisable_renderer()を呼び出してviewsではrenderができないようにした
 * 
 * @package Built-In
 */
namespace Wpails;
class Router{
  /** renderが呼び出せるか */
  private static bool $renderable = true;
  /** 初期化されているか */
  private static bool $is_inited = false;
  /** renderを無効にする */
  static function disable_renderer() :void{self::$renderable = false;}
  /** loginページへリダイレクト */
  static function redirect_to_login(string $redirect_url) :void{
    wp_safe_redirect(WPAILS_LOGIN_PATH.'?redirect_url='.$redirect_url);
    exit;
  }
  /** renderableじゃなかったらエラー */
  private static function throw_error_if_not_renderable() :void{
    if(!self::$renderable) throw new \ErrorException('WPAILS Error: Rendering is only available before controller ended.');
  }
  /** 500を表示 */
  static function render_500() :void{
    self::throw_error_if_not_renderable();
    status_header(500);
    require \file_path::views('_statics/500');
    exit;
  }
  /** 401を表示 */
  static function render_401() :void{
    self::throw_error_if_not_renderable();
    status_header(401);
    require \file_path::views('_statics/401');
    exit;
  }
  /** 404を表示 */
  static function render_404() :void{
    self::throw_error_if_not_renderable();
    status_header(404);
    require \file_path::views('_statics/404');
    exit;
  }
  /** 現在のパスを取得してRoute::initを呼び出す */
  static function init() :void{
    if(self::$is_inited) throw new ErrorException('WPAILS Error: Router is already inited!');
    $path = parse_url($_SERVER['REQUEST_URI'])['path'];
    $current_path = preg_replace(
      '/\/$/', '', $path
    );
    if($current_path === ''){
      $current_path = '/';// ルートの場合
    }
    \Wpails\Route::init(\Wpails\Config\routes(), $current_path);
    self::$is_inited = true;
  }
  /** リクエストされたルーティングが登録されているか検証 */
  static function render_404_if_route_doesnt_exist() :void{
    self::throw_error_if_not_renderable();
    $route = \Wpails\Route::now();
    if(!!$route){
      \Wpails\Module::init(\Wpails\Config\MODULES, $route);
    }else{
      self::render_404();
    }
  }
  /** リクエストされたルーティングにリダイレクトの値があればリダイレクト */
  static function redirect_if_current_route_has_redirect() :void{
    self::throw_error_if_not_renderable();
    $route = \Wpails\Route::now();
    \Wpails\Filter::init(\Wpails\Config\FILTERS);
    if(isset($route->redirect)){
      wp_safe_redirect($route->redirect, 302);
      exit;
    }
  }
  /** リクエストされたルーティングがユーザーを許可していなかったら404またはログインページへ */
  static function render_404_if_not_permitted() :void{
    self::throw_error_if_not_renderable();
    $route = \Wpails\Route::now();
    $filter = \Wpails\Filter::get($route->filter);
    $user_id = get_current_user_id();
    if($user_id){
      $meta = get_user_meta($user_id);
      $permission_array = $meta['permission'];
    }
    if(isset($permission_array) && count($permission_array) > 0){
      $permission = $permission_array[0];
    }
    $permission ??= 'unknown';
    if(!!$filter && !$filter->permits($permission)){// アクセス権限がないとき
      if(is_user_logged_in()){
        self::render_404();
      }else{
        $this_url = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        self::redirect_to_login($this_url);
      }
      exit;
    }
  }
}
