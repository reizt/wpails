<?php
/**
 * Monolog\Loggerをラップする
 * 
 * @package Built-In
 */
use \Monolog\Logger as L;
use \Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

class Logger{
  private static L $now;
  private static StreamHandler $stream;
  private static bool $inited = false;
  static function init(string $first_name):void{
    file_put_contents(get_theme_file_path() . '/' . Wpails\Config\LOGFILE, '');
    if(self::$inited === true) throw new ErrorException('WPAILS Error: Logger is already inited');
    $formatter = new LineFormatter(null, null, true);
    if(Wpails\Config\ENV === 'development'){
      self::$stream = new StreamHandler(Wpails\Config\LOGFILE, L::DEBUG);
    }else{
      self::$stream = new StreamHandler(Wpails\Config\LOGFILE, L::WARNING);
    }
    self::$stream->setFormatter($formatter);
    self::set_name($first_name);
    self::$inited = true;
  }
  static function set_name(string $name):void{
    self::$now = new L($name);
    self::$now->pushHandler(self::$stream);
  }
  private static function arranged_str(mixed $v):string{
    return str_replace('    ', '  ', print_r($v, true));
  }
  static function debug(mixed $v):void{self::$now->debug(self::arranged_str($v));}
  static function info(mixed $v):void{self::$now->info(self::arranged_str($v));}
  static function notice(mixed $v):void{self::$now->notice(self::arranged_str($v));}
  static function warning(mixed $v):void{self::$now->warning(self::arranged_str($v));}
  static function error(mixed $v):void{self::$now->error(self::arranged_str($v));}
  static function critical(mixed $v):void{self::$now->critical(self::arranged_str($v));}
  static function alert(mixed $v):void{self::$now->alert(self::arranged_str($v));}
  static function emergency(mixed $v):void{self::$now->emergency(self::arranged_str($v));}
}
