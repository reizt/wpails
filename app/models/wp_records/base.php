<?php
/**
 * \Wpails\WP_Record trait
 * 
 * Post, Userに共通したメソッドをまとめたtrait
 * 
 * @package Models
 */
namespace Wpails;
trait WP_Record{
  /** DBに保存されているレコードかどうか */
  protected bool $is_saved = false;
  /** デフォルトでWPのテーブルにあるカラムの[キー=>値] */
  protected array $primary = [];
  /** メタのカラムの[キー=>値] */
  protected array $meta = [];
  /** 保存・作成に失敗したときはここに入れる */
  protected ?WP_Error $error = null;
  function __construct(){
    $this->is_saved = false;
    $this->assign(static::default_attributes());
  }
  /** $is_savedのゲッター */
  function is_saved() :bool{
    return $this->is_saved;
  }
  /** カラムの一覧を返す */
  protected static function COLUMNS() :array{
    // ORIGINALによってFIXEDが書き換えられる可能性がある
    return array_merge(static::ORIGINAL_COLUMNS(), static::DEFAULT_COLUMNS());
  }
  /** Post, Userのdefault_attributes()で共通の関数 */
  private static function default_attributes_prev() :array{
    $result = [];
    foreach(static::COLUMNS() as $col){
      if(isset($col['default'])){
        $result[$col['name']] = $col['default'];
      }
    }
    return $result;
  }
  /**
   * デフォルトのカラムかどうか
   * @param string $column カラム名
   */
  protected static function is_primary_column(?string $column) :bool{
    return in_array($column, static::PRIMARY_COLUMN_NAMES());
  }
  /**
   * メタのカラムかどうか
   * @param string $column カラム名
   * @return bool
   */
  protected static function is_meta_column(?string $column) :bool{
    $meta_columns = array_diff(
      array_map_by_key('name', static::COLUMNS()),
      static::PRIMARY_COLUMN_NAMES()
    );
    return in_array($column, $meta_columns);
  }
  /**
   * すべてのレコードを返す関数
   * @param array $options 内部的にsearchを実行するときそのまま渡される
   */
  static function all(array $options = []) :array{
    return static::search($options);
  }
  /**
   * get_postsもしくはget_usersに渡せば期待した結果が返るようなクエリをわかりやすい入力方法で組み立ててくれる
   * @param array $options
   *  オプションをkey=>valueで指定できる(現時点: equals, orderby, order, keyword, date_query, post__in, include)
   */
  private static function build_query(array $options = []) :array{
    $query = ['meta_query' => []];
    if(isset($options['equals'])){// key => valueで値が一致するクエリ
      foreach($options['equals'] as $key => $value){
        if(static::is_primary_column($key)){
          if($key === 'post_name'){// get_postsの引数に渡すときはpost_nameではなくnameでなければならないらしい
            $query['name'] = $value;
          }else{
            $query[$key] = $value;
          }
        }elseif(static::is_meta_column($key)){
          array_push($query['meta_query'], ['key' => $key, 'value' => $value]);
        }
      }
    }
    if(isset($options['orderby'])){// 並び替え
      $orderby = $options['orderby'];
      if(static::is_primary_column($orderby)){
        $query['orderby'] = $orderby;
      }elseif(static::is_meta_column($orderby)){
        $query['orderby'] = 'meta_value';
        $query['meta_key'] = $orderby;
      }
    }
    if(isset($options['keyword'])){// NOTE: テーブル・カラムによって検索方法がかなり違うがすべてkeywordで統一
      if(in_array(get_called_class(), ['Client', 'Employee'])){
        $query['search'] = '*' . $options['keyword']. '*';
      }else{
        $query['s'] = $options['keyword'];
      }
    }
    // NOTE: そのまま渡すオプション
    // orderはデフォルトでDESC
    $rare_options = ['order', 'date_query', 'post__in', 'include'];
    foreach($rare_options as $option){
      if(isset($options[$option])){
        $query[$option] = $options[$option];
      }
    }
    \Logger::debug($query);
    return $query;
  }
  /**
   * 条件に合ったユーザーを1件だけ返す
   * @param array $properties [カラム名=>値] searchのequalsオプションにそのまま渡される
   * @return Post|User|null ヒットしなければnull
   * @see Post::search, User::search
   */
  static function find_by(array $properties) :mixed{
    $users = static::search(['equals'=>$properties, 'limit'=>1]);
    return count($users) > 0 ? array_shift($users) : null;
  }
  /**
   * [キー=>値]の連想配列を受け取って$primary, $metaに適切な型で振り分ける
   * @param array $attrs [キー=>値, ...]
   */
  function assign(array $attrs) :void{
    $int_column_names = array_map_by_key(
      'name', array_filter(
        static::COLUMNS(), function(array $col) :bool{
          return isset($col['type']) && $col['type'] === 'int';
        }
      )
    );
    foreach($attrs as $key => $value){
      if(in_array($key, $int_column_names)){
        $value = intval($value);
      }
      if(static::is_primary_column($key)){
        $this->primary[$key] = $value;
      }elseif(static::is_meta_column($key)){
        $this->meta[$key] = $value;
      }
    }
  }
  /** すでにあるレコードなら保存、まだ作成されていなければ作成 */
  function save() :bool{
    if($this->is_saved()){
      return $this->update(array_merge($this->primary, $this->meta));
    }else{
      $record = static::create(array_merge($this->primary, $this->meta));
      return $record->is_saved;
    }
  }
  /** カラム名で指定すれば値が返ってくる
   * @param string $attr
   * @return mixed
   */
  function attribute(?string $attr) :mixed{
    if(static::is_primary_column($attr) && isset($this->primary[$attr])){
      return $this->primary[$attr];
    }elseif(static::is_meta_column($attr) && isset($this->meta[$attr])){
      return $this->meta[$attr];
    }
    return null;
  }
  /**
   * バリデーションをチェックする
   * @return bool 保存できるかどうか
  */
  function is_valid() :bool{
    $column_validations = [];
    foreach(static::COLUMNS() as $column){
      if(isset($column['validations']) && is_array($column['validations'])){
        $column_validations[$column['name']] = $column['validations'];
      }
    }
    foreach($column_validations as $name => $validations){
      $v = $this->attribute($name);
      if(isset($validations['required']) && $validations['required']){
        if(is_null_or_empty($v)) return false;
      }
    }
    return true;
  }
  /** エラーメッセージを取得 */
  function error_message() :string{
    if(isset($this->error) && is_wp_error($this->error)){
      return $this->error->get_error_message();
    }else{
      if(static::POST_TYPE !== null){
        $TYPE = static::POST_TYPE;
      }elseif(static::USER_TYPE !== null){
        $TYPE = static::USER_TYPE;
      }
      return $TYPE . 'の保存に失敗しました。';
    }
  }
}
