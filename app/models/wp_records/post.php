<?php
/**
 * Postクラス
 * 
 * 投稿をオブジェクトでラップするクラス
 * 
 * @package Models
 * @see models/wp_records/base.php
 */
class Post{
  use \Wpails\WP_Record;
  /** @var string 投稿タイプ */
  protected const POST_TYPE = 'post';
  /** @var string[] wp_postsのカラム */
  private const PRIMARY_COLUMN_NAMES = [
    'ID', 'post_type', 'post_author', 'post_status', 'post_title',
    'post_date', 'post_modified',
    'post_content', 'post_name', 'post_parent', 'comment_status',
  ];
  /** @var array[] デフォルトのカラム */
  private const DEFAULT_COLUMNS = [
    ['name'=>'ID', 'label'=> 'ID', 'type'=>'int'],
    ['name'=>'post_status', 'label'=> 'ステータス', 'default'=>'publish'],
  ];
  /** @var array[] 固有のカラム */
  protected const ORIGINAL_COLUMNS = [
    ['name'=>'post_type', 'label'=> '投稿タイプ', 'default'=>'post'],
  ];
  /** WP_RecordがPRIMARY_COLUMN_NAMESを取得するためのゲッター */
  private static function PRIMARY_COLUMN_NAMES() :?array{return self::PRIMARY_COLUMN_NAMES;}
  /** WP_RecordがDEFAULT_COLUMNSを取得するためのゲッター */
  private static function DEFAULT_COLUMNS() :?array{return self::DEFAULT_COLUMNS;}
  /** WP_RecordがORIGINAL_COLUMNSを取得するためのゲッター */
  private static function ORIGINAL_COLUMNS() :?array{return static::ORIGINAL_COLUMNS;}
  /**
   * COLUMNSをパースしてデフォルト値をセットした配列を返す
   * @return array [カラム名=>デフォルト値, ...]
   */
  protected static function default_attributes() :array{
    $result = static::default_attributes_prev();
    $result['post_type'] = static::POST_TYPE;
    return $result;
  }
  /** すべての投稿IDを配列で返す */
  static function ids() :array{
    return get_posts([
      'post_type'=>static::POST_TYPE,
      'posts_per_page'=>-1,
      'fields'=>'ids',
    ]);
  }
  /**
   * WP_Recordのbuild_query()でクエリを計算してget_postsを実行
   * @param array $properties [カラム名=>値]
   */
  static function search($options = []) :array{
    $limit = (isset($options['limit'])) ? $options['limit'] : -1;
    $options['equals'] ??= [];
    $options['equals'] = array_merge(
      $options['equals'], [
        'post_type'=>static::POST_TYPE,
        'posts_per_page' => $limit,
      ]
    );
    $wp_posts = get_posts(static::build_query($options));
    return array_map(function($wp_post){
      $p = new static;
      return $p->parse_wp_post($wp_post);
    }, $wp_posts);
  }
  /**
   * WP_Postオブジェクトを読み込んで自分のプロパティにセットする
   * @param WP_Post $wp_post
   * @return Post
   */
  private function parse_wp_post(WP_Post $wp_post) :object{
    $this->primary = [];
    foreach(self::PRIMARY_COLUMN_NAMES as $col){
      $this->primary[$col] = $wp_post->$col;
    }
    $this->meta = [];
    foreach(get_post_custom($wp_post->ID) as $key => $value_array){
      $this->meta[$key] = $value_array[0];
    }
    $this->assign(array_merge($this->primary, $this->meta));
    $this->is_saved = true;
    return $this;
  }
  /**
   * IDで投稿を探して返す
   * @param int $id
   * @return Post|null なければnull
   */
  static function find(?int $id){
    if($id === null) return null;
    $post = new static;
    $wp_post = get_post($id);
    if(is_null($wp_post)){
      return null;
    }else{
      $post->parse_wp_post($wp_post);
      return $post;
    }
  }
  /**
   * 投稿を作成する
   * @param array $attrs [カラム名=>値]
   * @return Post 失敗しても作成途中のインスタンスが返る
   */
  static function create(array $attrs) :object{
    $post = new static;
    $post->assign($attrs);
    $meta_array = [];
    foreach($post->meta as $key => $value){
      array_push($meta_array, ['key' => $key, 'value' => $value]);
    }
    if(!$post->is_valid()) return $post;
    $insert_result = wp_insert_post(
      array_merge(
        $post->primary, ['meta_input' => $post->meta]
      )
    );
    if(is_wp_error($insert_result)){
      $post->error = $insert_result;
      $post->has_error = true;
      \Logger::debug($insert_result->get_error_message());
      \Logger::debug('-- Failed to create ' . static::POST_TYPE);
    }else{
      $post_id = $insert_result;
      $post->assign(['ID' => $post_id]);
      $post->is_saved = true;
      \Logger::debug('-- Create ' . static::POST_TYPE);
    }
    return $post;
  }
  /**
   * 投稿を更新する
   * @param array $attrs [カラム名=>値]
   * @return bool 更新に成功したか
   */
  function update(array $attrs) :bool{
    $id = $this->attribute('ID');
    $meta_before = $this->meta;
    $this->assign(array_merge($attrs, ['ID' => $id]));
    if(!$this->is_valid()) return false;
    $primary_update_result = wp_update_post($this->primary, true);
    if(is_wp_error($primary_update_result)){
      $this->has_error = true;
      $this->error = $primary_update_result;
      \Logger::debug($this->error->get_error_message());
      return false;
    }
    \Logger::debug($meta_before, $this->meta);
    foreach($this->meta as $key => $value){
      $successed = update_post_meta($id, $key, $value);
      if((isset($meta_before[$key]) && $meta_before[$key] != $value) && !$successed){// 値が変更されていて更新が成功しなかった場合
        $this->has_error = true;
        return false;
      }
    }
    \Logger::debug('-- Update ' . static::POST_TYPE);
    return true;
  }
  /**
   * 投稿を削除する
   * @param bool $force WP_Postのforce_deleteに対応する ゴミ箱に残すかどうか
   * @return bool 削除に成功したらtrue
   */
  function destroy(bool $force = false) :bool{
    $res = wp_delete_post($this->primary['ID'], $force);
    \Logger::debug('-- Delete ' . static::POST_TYPE);
    if($res != false) \Logger::debug('-- Delete ' . static::POST_TYPE);
    return !!$res;
  }
}
