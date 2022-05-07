<?php
/**
 * WPAILSがデフォルトで設定しているアクションフック
 * 
 * @package Built-In
 */
/**
 * Add support for the "display_name" search column in WP_User_Query
 * 
 * @see http://wordpress.stackexchange.com/a/166369/26350
 */
add_filter('user_search_columns', function($search_columns){
  $search_columns[] = 'display_name';
  return $search_columns;
});
