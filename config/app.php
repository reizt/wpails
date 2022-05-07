<?php
/**
 * 設定ファイル
 * 
 * @package Config
 */
namespace Wpails\Config;
require \file_path::config('routes');
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
const WPAILS_LOGIN_PATH = '/login';
