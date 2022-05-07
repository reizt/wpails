<?php
/**
 * ファイルのパスを取得するための関数をまとめたファイル
 * 
 * WPの関数であるget_theme_file_path()を使ってもいいが、
 * 関数でラップすることでタイポを検出しやすい(Undefined functionにより)
 * アセットファイルなどのURL形式を望むケースと、PHPファイルなどのパス形式を望むケースを区別しなくて済む
 * また、ここに登録されていないディレクトリ(/binなど)は基本的に取得しないという意味もある
 * 
 * @package Built-In
 */
class file_path{
  /** URL形式でテーマディレクトリのassetsのルートを返す */
  private static function asset_base(string $name) :string{
    return home_url('wp-content/themes/' . get_template() . '/app/assets/' . $name);
  }
  /** URL形式でテーマのJSファイルを返す(拡張子なし) */
  static function js(string $name) :string{
    return self::asset_base('js/'.$name.'.js');
  }
  /** URL形式でテーマのCSSファイルを返す(拡張子なし) */
  static function css(string $name) :string{
    return self::asset_base('css/'.$name.'.css');
  }
  /** URL形式でテーマのメディアファイルを返す(拡張子あり) */
  static function media(string $name) :string{
    return self::asset_base('media/' . $name);
  }
  /** テーマ直下のファイル名を返す(拡張子あり) */
  static function base(string $name) :string{
    return get_theme_file_path() . '/' . $name;
  }
  /** テーマ直下のPHPファイル名を返す(拡張子なし) */
  private static function base_php(string $name) :string{
    return self::base($name . '.php');
  }
  /** /config以下のPHPファイル名を返す(拡張子なし) */
  static function config(string $name) :string{
    return self::base_php('config/' . $name);
  }
  /** /controllers以下のPHPファイル名を返す(拡張子なし) */
  static function controllers(string $name) :string{
    return self::base_php('app/controllers/' . $name);
  }
  /** /models以下のPHPファイル名を返す(拡張子なし) */
  static function models(string $name) :string{
    return self::base_php('app/models/' . $name);
  }
  /** /helpers以下のPHPファイル名を返す(拡張子なし) */
  static function helpers(string $name) :string{
    return self::base_php('app/helpers/' . $name);
  }
  /** /views以下のPHPファイル名を返す(拡張子なし) */
  static function views(string $name) :string{
    return self::base_php('app/views/' . $name);
  }
}
