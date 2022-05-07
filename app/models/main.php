<?php
/**
 * Modelsのエントリポイント
 * 
 * @package Models
 */
require \file_path::models('wp_records/base');
require \file_path::models('wp_records/post');
foreach(glob(\file_path::models('wp_records/post_ancestors/*')) as $f){
  require $f;
}
require \file_path::models('wp_records/user');
foreach(glob(\file_path::models('wp_records/user_ancestors/*')) as $f){
  require $f;
}
foreach(glob(\file_path::models('utilities/*')) as $f){
  require $f;
}
