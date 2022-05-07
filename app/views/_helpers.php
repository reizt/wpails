<?php
function stylesheet_tag(string $file_name) :void{
  $file = \file_path::css($file_name);
  ?>
    <link rel="stylesheet" href="<?= $file ?>">
  <?php
}
function javascript_tag(string $file_name) :void{
  $file = \file_path::js($file_name);
  ?>
    <script src="<?= $file ?>"></script>
  <?php
}
function form_trigger_tag(string $name) :void{
  ?>
    <input type="hidden" name="<?= $name ?>_trigger">
  <?php
}
function form_id_tag(object $record) :void{
  ?>
    <input type="hidden" name="id" value="<?= $record->attribute('ID') ?>">
  <?php
}
