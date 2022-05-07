<?php
/**
 * ページ固有のアクションが実行される前に実行される処理を記述
 * 
 * 全ページの共通処理
 * 
 * @package Controllers
 */
session_start();
if($_SERVER['REQUEST_METHOD'] === 'POST'){
  if(!isset($_POST['csrf-token']) || $_POST['csrf-token'] !== $_SESSION['csrf-token']){
    \Wpails\Router::render_401();
    exit;
  }
}
$TOKEN_LENGTH = 32;
$bytes = random_bytes($TOKEN_LENGTH);
$_SESSION['csrf-token'] = bin2hex($bytes);
