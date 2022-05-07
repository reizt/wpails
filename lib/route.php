<?php
/**
 * \Wpails\Routeクラスのファイル
 * 
 * ルーティングが持つ情報をオブジェクトに持たせる
 * ルーティングの設定を解析して現在のルートを算出する
 * $nowをprivateにしてゲッターのみを公開。
 * さらに$is_initedを一方通行にして一度しか初期化できないようにしている
 * 
 * @package Built-In
 */
namespace Wpails;
class Route{
  public ?string $path; /** /から始まるパス */
  public ?string $module; /** 所属するモジュール */
  public ?string $file_path; /** ControllerとViewのファイルパス(controllers/, views/以降の) */
  public ?string $slug; /** パスの最後の単語 */
  public ?string $title; /** ページのタイトル */
  public ?string $filter; /** フィルター名 */
  public ?string $redirect; /** リダイレクト先 */
  public bool $no_view = false; /** ビューファイルを使わない場合true(Controller実行後リダイレクトすることを想定) */
  /** ルーティングの設定をRouteインスタンスの配列に変換して格納 */
  private static array $flat_routes = [];
  /** 現在のリクエストに対応したRouteインスタンス */
  private static ?Route $now;
  /** 初期化されているかどうか */
  private static bool $is_inited = false;
  function __construct($path, $module, $file_path, $slug, $title, $filter, $redirect, $no_view){
    $this->path = $path;
    $this->module = $module;
    $this->file_path = $file_path;
    $this->slug = $slug;
    $this->title = $title;
    $this->filter = $filter;
    $this->redirect = $redirect;
    if($no_view === true) $this->no_view = true;
  }
  /**
   * ルーティングの配列をフラットなRouteインスタンスの配列に直して
   * 現在のルートを検索し、self::$nowに格納
   * @param array $routes config/WPAILS_APP_NAME.phpで設定したroutes()を入れる
   * @param string $current_path リクエストされたパスを/から始まる形で
   * @see self::calc_flat_routes
   */
  static function init(array $routes, string $current_path) :void{
    if(self::$is_inited) throw new \ErrorException('WPAILS Error: Route is already inited!');
    self::calc_flat_routes($routes);
    $match = array_filter(
      self::$flat_routes, function($route) use($current_path){
        return $route->path === $current_path;
      }
    );
    if(count($match) > 0){
      self::$now = array_shift($match);
    }else{
      self::$now = null;
    }
    self::$is_inited = true;
  }
  /** self::$flat_routesのゲッター */
  static function flat_routes() :array{
    return self::$flat_routes;
  }
  /** self::$nowのゲッター */
  static function now() :?Route{
    return self::$now;
  }
  /**
   * calc_flat_routesにおいてrouteに余計な値が入っていないかチェックする
   * 
   * ルーティング設定は複雑化しやすいためtypo検知にも役立つ
   * 
   * @param array $route ルーティングの連想配列
   */
  private static function validate_route_array(array $route) :void{
    if(!isset($route['slug'])){
      throw new \ErrorException(
        "WPAILS Error: Route array must include value 'slug'.\n"
        ."Please check config/" . WPAILS_APP_NAME . ".php or files it includes. and modify the function routes()."
      );
      exit;
    }
    $unknown_options = array_diff(
      array_keys($route), ['slug', 'title', 'is_namespace', 'filter', 'redirect', 'no_view', 'no_nested_url', 'ancestors']
    );
    if(count($unknown_options) > 0){
      throw new \ErrorException("WPAILS Error: Unknown options " . print_r($unknown_options));
      exit;
    }
  }
  /**
   * @param array $routes ルーティングの配列
   * @param string $prev スラッグの前につけてパスにする
   * @param string $filter $routes全体に適用するフィルター名
   * @param array{string name: モジュール名, bool|null no_nested_url: モジュール名をURLに含まない}
   */
  private static function calc_flat_routes(
      array $routes,
      ?string $prev = null,
      ?string $filter = null,
      array $module = []):void
    {
    foreach($routes as $route){
      self::validate_route_array($route);
      /** パスからモジュール名を抽出するクロージャ */
      $extract_module = function($p){
        if(preg_match('/^\/?([^\/]+)\/?$/', $p)){
          return str_replace('/', '', $p);
        }else{
          return preg_replace('/^\/?([^\/]+)\/.+/', '$1', $p);
        }
      };
      $path = $prev . $route['slug'];
      $module_child = [];
      if(count($module) === 0){
        $module_child['name'] = $extract_module($path);
        $module_child['no_nested_url'] = @$route['no_nested_url'];
      }else{
        $module_child = $module;// NOTE: モジュールがすでにあるならそのまま受け継ぐ
      }
      if(@!!$module_child['no_nested_url']){
        $url = str_replace($module_child['name'], '', $path);
      }else{
        $url = $path;
      }
      $url = '/' . preg_replace('/^\//', '', $url);// NOTE: スラッシュが必ずつくように
      $file_path = preg_replace('/^\//', '', $path);
      if($file_path === '') $file_path = '_root';// ルートの場合
      $is_namespace = isset($route['is_namespace']) && $route['is_namespace'];
      $filter_child = isset($route['filter']) ? $route['filter'] : $filter;// NOTE: 自分のfilterがなければ親のfilterを継承
      if(!$is_namespace){
        array_push(
          self::$flat_routes, new self(
            $url,
            $module_child['name'],
            $file_path,
            $route['slug'],
            @$route['title'],// NOTE: titleはなくても無視
            $filter_child,
            @$route['redirect'],// NOTE: redirectはなくても無視
            @$route['no_view'],// NOTE: no_viewはなくても無視
          )
        );
      }
      $ancestors = @$route['ancestors'];
      if(is_array($ancestors) && count($ancestors) > 0){
        // NOTE: 子ルートがあれば再帰呼び出し
        self::calc_flat_routes($ancestors, $file_path . '/', $filter_child, $module_child);
      }
    }
  }
}
