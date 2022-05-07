<?php
/**
 * Userクラス
 * 
 * ユーザーをオブジェクトでラップするクラス
 * 
 * @package Models
 * @see models/wp_records/base.php
 */
class User{
  use \Wpails\WP_Record;
  /** @var string ユーザータイプ */
  protected const USER_TYPE = null;
  /** @var string[] wp_usersのカラム */
  private const PRIMARY_COLUMN_NAMES = [
    'ID', 'user_login', 'user_pass', 'user_nicename', 'user_url', 'user_email', 'display_name', 'role'
  ];
  /** @var array[] デフォルトのカラム */
  private const DEFAULT_COLUMNS = [
    ['name'=>'ID', 'label'=>'ID', 'type'=>'int'],
    ['name'=>'user_login', 'label'=>'ログインID', 'validations'=>['required'=>true]],
    ['name'=>'user_type', 'label'=>'ユーザータイプ'],
    ['name'=>'user_nicename', 'label'=>'URLネーム'],
    ['name'=>'user_url', 'label'=>'URL'],
    ['name'=>'user_email', 'label'=>'メールアドレス', 'validations'=>['required'=>true]],
    ['name'=>'role', 'label'=>'権限'],
    ['name'=>'permission', 'label'=>'アクセス権限', 'validations'=>['required'=>true]],
  ];
  /** @var array[] 固有のカラム */
  protected const ORIGINAL_COLUMNS = [
  ];
  /** WP_RecordがPRIMARY_COLUMN_NAMESを取得するためのゲッター */
  private static function PRIMARY_COLUMN_NAMES(){return self::PRIMARY_COLUMN_NAMES;}
  /** WP_RecordがDEFAULT_COLUMNSを取得するためのゲッター */
  private static function DEFAULT_COLUMNS(){return self::DEFAULT_COLUMNS;}
  /** WP_RecordがORIGINAL_COLUMNSを取得するためのゲッター */
  private static function ORIGINAL_COLUMNS(){return static::ORIGINAL_COLUMNS;}// HACK: traitで使うためにゲッターを作る
  /**
   * COLUMNSをパースしてデフォルト値をセットした配列を返す
   * @return array [カラム名=>デフォルト値, ...]
   */
  protected static function default_attributes(){
    $result = static::default_attributes_prev();
    $result['user_type'] = static::USER_TYPE;
    return $result;
  }
  /** すべてのユーザーIDを配列で返す
   * @return int[]
   */
  static function ids(){
    return get_users([
      'fields' => 'ids',
      'users_per_page' => -1,
      'meta_query' => [
        ['key'=>'user_type', 'value'=>static::USER_TYPE],
      ]
    ]);
  }
  /**
   * WP_Recordのbuild_query()でクエリを計算してget_usersを実行
   * @param array $properties [カラム名=>値]
   * @return User[]
   */
  static function search($options = []){
    $limit = (isset($options['limit'])) ? $options['limit'] : -1;
    $options['equals'] ??= [];
    $options['equals'] = array_merge(
      $options['equals'], [
        'user_type'=>static::USER_TYPE,
        'users_per_page' => $limit,
      ]
    );
    $wp_users = get_users(static::build_query($options));
    return array_map(function(WP_User $wp_user) :object{
      $p = new static;
      return $p->parse_wp_user($wp_user);
    }, $wp_users);
  }
  /**
   * WP_Userオブジェクトを読み込んで自分のプロパティにセットする
   * @param WP_User $wp_user
   * @return User
   */
  private function parse_wp_user(WP_User $wp_user){
    foreach(self::PRIMARY_COLUMN_NAMES as $col){
      if($col === 'role'){
        $roles = $wp_user->roles;
        if(count($roles) > 0){
          $this->primary[$col] = $roles[0];
        }
      }else{
        $this->primary[$col] = $wp_user->$col;
      }
    }
    $this->meta = [];
    foreach(get_user_meta($wp_user->ID) as $key => $value_array){
      $this->meta[$key] = $value_array[0];
    }
    $this->assign(array_merge($this->primary, $this->meta));
    $this->is_saved = true;
    return $this;
  }
  /**
   * IDでユーザーを探して返す
   * @param int $id ないとエラーになる
   * @return User|null なければnull
   */
  static function find(?int $id){
    if($id === null) throw new ErrorException('ID must exists.');
    $user = new static;
    $wp_user = get_userdata($id);
    if($wp_user === false){
      return null;
    }else{
      $user->parse_wp_user($wp_user);
      return $user;
    }
  }
  /**
   * ユーザーを作成する
   * @param array $attrs [カラム名=>値]
   * @return User 失敗しても作成途中のインスタンスが返る
   */
  static function create(array $attrs) :object{
    $user = new static;
    $user->assign($attrs);
    $meta_array = [];
    foreach($user->meta as $key => $value){
      array_push($meta_array, ['key' => $key, 'value' => $value]);
    }
    if(!$user->is_valid()) return $user;
    $insert_result = wp_insert_user(
      array_merge(
        $user->primary,
        ['user_login'=>$user->attribute('email'), 'meta_input'=>$meta_array]
      )
    );
    if(!is_wp_error($insert_result)){
      $user->error = $insert_result;
      $user->has_error = true;
      \Logger::debug($insert_result->get_error_message());
      \Logger::debug('-- Failed to create ' . static::USER_TYPE);
    }else{
      $user_id = $insert_result;
      $user->assign(['ID' => $user_id]);
      $user->is_saved = true;
      \Logger::debug('-- Create ' . static::USER_TYPE);
    }
    $user->assign(['ID' => $user_id]);
    return $user;
  }
  /**
   * ユーザーを更新する
   * @param array $attrs [カラム名=>値]
   * @return bool 更新に成功したか
   */
  function update(array $attrs) :bool{
    $id = $this->attribute('ID');
    $meta_before = $this->meta;
    $this->assign(array_merge($attrs, ['ID' => $id]));
    $result = wp_update_user($this->primary);
    if(is_wp_error($result)){
      \Logger::debug($result->get_error_message());
      \Logger::debug('-- Failed to update ' . static::USER_TYPE);
      return false;
    }
    foreach($this->meta as $key => $value){
      $result = update_user_meta($id, $key, $value);
      if(@$meta_before[$key] != $value && !$result){
        \Logger::debug('-- Failed to update meta of ' . static::USER_TYPE);
        return false;// 値が変更されていて成功しなかった場合
      }
    }
    \Logger::debug('-- Update ' . static::USER_TYPE);
    return true;
  }
  /**
   * ユーザーを削除する
   * @param bool $force WP_Userのforce_deleteに対応する ゴミ箱に残すかどうか
   * @return bool 削除に成功したらtrue
   */
  function destroy(bool $force = false) :bool{
    $res = wp_delete_user($this->primary['ID'], $force);
    if($res != false) \Logger::debug('-- Delete ' . static::USER_TYPE);
    return !!$res;
  }
}
