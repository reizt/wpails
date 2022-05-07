<?php
namespace Wpails;
if(\Wpails\Config\ENV === null || !in_array(\Wpails\Config\ENV, ['development', 'production'])){
  throw new \ErrorException('Wpails\Config\ENV must be development or production');
}
if(Config\PRESET === null || !is_array(Config\PRESET)){
  throw new \ErrorException('WPAILS error - Invalid config PRESET: '.PRESET.' | It must be array');
}
if(Config\PRESET === null || !is_array(Config\PRESET)){
  throw new \ErrorException('WPAILS error - Invalid config PRESET: '.PRESET.' | It must be array');
}
if(Config\DEVELOPMENT === null || !is_array(Config\DEVELOPMENT)){
  throw new \ErrorException('WPAILS error - Invalid config DEVELOPMENT: '.DEVELOPMENT.' | It must be array');
}
if(Config\PRODUCTION === null || !is_array(Config\PRODUCTION)){
  throw new \ErrorException('WPAILS error - Invalid config PRODUCTION: '.PRODUCTION.' | It must be array');
}
if(Config\MODULES === null || !is_array(Config\MODULES)){
  throw new \ErrorException('WPAILS error - Invalid config MODULES: '.MODULES.' | It must be array');
}
if(Config\FILTERS === null || !is_array(Config\FILTERS)){
  throw new \ErrorException('WPAILS error - Invalid config FILTERS: '.FILTERS.' | It must be array');
}
if(Config\PERMISSIONS === null || !is_array(Config\PERMISSIONS)){
  throw new \ErrorException('WPAILS error - Invalid config PERMISSIONS: '.PERMISSIONS.' | It must be array');
}
