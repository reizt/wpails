<?php
/**
 * WPAILSのメインファイル
 * 
 * @package Built-In
 * @see lib/*.php
 */
namespace Wpails;
require get_template_directory() . '/lib/file_path.php';

require \file_path::base('vendor/autoload.php');
require \file_path::config('app');
require \file_path::base('lib/logger.php');
\Logger::init('rampup_system');
require \file_path::base('lib/validate_config.php');

require \file_path::base('lib/hooks.php');
require \file_path::base('lib/support.php');
require \file_path::base('lib/interface.php');
require \file_path::base('lib/module.php');
require \file_path::base('lib/route.php');
require \file_path::base('lib/filter.php');
require \file_path::base('lib/router.php');
/**
 * @var bool WP SCSSでページが読み込まれたら必ずCSSをコンパイルするかどうか
 * デフォルトではapp/assets/scss直下のファイルが変更されないとコンパイルされないので
 * 開発環境ではこれをtrueにする
 */
if(is_development()){
  define('WP_SCSS_ALWAYS_RECOMPILE', true);
}
\Logger::set_name('rampup_preload');
foreach(glob(\file_path::helpers('preload/*')) as $f){
  require $f;
}
\Logger::set_name('action_hooks');
require \file_path::config('action_hooks');
