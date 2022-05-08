<?php
/**
 * 設定ファイル
 * 
 * @package Config
 */
namespace Wpails\Config;
const ENV = 'development';
// const ENV = 'production';
const LOGFILE = 'logs/app.'.ENV.'.log';
const PRESET = [
  'log_level' => 'debug',
];
const DEVELOPMENT = [
  'log_level' => 'debug',
];
const PRODUCTION = [
  'log_level' => 'warn',
];
const MODULES = [
  'hello',
];
const FILTERS = [
  'logged_in'=>[
    'admin', 'staff', 'client',
  ],
  'only_admin'=>[
    'admin'
  ],
];
const PERMISSIONS = ['admin', 'staff', 'client'];
const LOGIN_PATH = '/login';
