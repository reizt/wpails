<?php
/**
 * ページ固有のアクションが実行された後に実行される処理を記述
 * 
 * 全ページの共通処理
 * 
 * @package Controllers
 */
if(count($_GET) > 0) \Logger::debug($_GET);
if(count($_POST) > 0) \Logger::debug($_POST);
