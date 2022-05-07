<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="<?= $_SESSION['csrf-token'] ?>">
  <meta name="request-method" content="<?= $_SERVER['REQUEST_METHOD'] ?>">
  <title><?= $WPAILS_REQUEST->route->title ?> - <?php bloginfo('name'); ?></title>
  <?= stylesheet_tag('style') ?>
  <?php if($WPAILS_MODULE) : ?>
    <?= stylesheet_tag($WPAILS_MODULE)// モジュール固有のスタイルシートを読む ?>
  <?php endif; ?>
  <?php
    if(file_exists($LOADED_FILES['module_head'])){
      include $LOADED_FILES['module_head'];
    }
  ?>
  <?php wp_head(); ?>
</head>
