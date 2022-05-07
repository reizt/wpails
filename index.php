<?php
/**
 * ビューファイルの起点
 * 
 * @package Views
 * @see prepare.php $WPAILS_MODULE, $LOADED_FILES, $WPAILS_REQUESTはここで定義
 */
require \file_path::base('prepare.php');
\Logger::set_name('controller');
require \file_path::controllers('_helpers');
require \file_path::controllers('_before_action');
if(file_exists($LOADED_FILES['controller'])){
  require $LOADED_FILES['controller'];
}
require \file_path::controllers('_after_action');
// テンプレートを使用しないルーティング OR テンプレートファイルが見つからない
if($WPAILS_REQUEST->route->no_view === true || !file_exists($LOADED_FILES['template'])){
  \Wpails\Router::render_404();
}
\Logger::set_name('view');
Logger::debug('view reached');
\Wpails\Router::disable_renderer();
require \file_path::views('_helpers');
if(file_exists($LOADED_FILES['module_views_helper'])){
  require $LOADED_FILES['module_views_helper'];
}
?>
<!DOCTYPE html>
<html <?= language_attributes() ?>>
  <?php include \file_path::views('_layouts/_head') ?>
  <body data-module="<?= $WPAILS_MODULE ?>">
    <?php if(\Wpails\is_development()){
      include \file_path::views('_layouts/_development');
    } ?>
    <?php
      include \file_path::views('_layouts/_header');
      // モジュール固有のindex.phpがあれば読むが
      // なければそのままテンプレートファイルを読む
      if(file_exists($LOADED_FILES['module_template'])){
        include $LOADED_FILES['module_template'];
      }else{
        include $LOADED_FILES['template'];
      }
      ?>
    <?= javascript_tag('index') ?>
    <?php if(!!$WPAILS_MODULE) : ?>
      <?= javascript_tag($WPAILS_MODULE) ?>
    <?php endif; ?>
  </body>
</html>
<?php
require \file_path::controllers('_after_render');
