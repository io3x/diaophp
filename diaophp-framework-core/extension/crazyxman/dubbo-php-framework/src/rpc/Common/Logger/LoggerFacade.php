<?php
/*
  +----------------------------------------------------------------------+
  | dubbo-php-framework                                                        |
  +----------------------------------------------------------------------+
  | This source file is subject to version 2.0 of the Apache license,    |
  | that is bundled with this package in the file LICENSE, and is        |
  | available through the world-wide-web at the following url:           |
  | http://www.apache.org/licenses/LICENSE-2.0.html                      |
  +----------------------------------------------------------------------+
  | Author: Jinxi Wang  <crazyxman01@gmail.com>                              |
  +----------------------------------------------------------------------+
*/

namespace Dubbo\Common\Logger;
class logger {
    private $logfile_name;
    private $cp;
    private $line;
    public function __construct($logfile_name='debug',$cp='',$line='')
    {
        $this->logfile_name = $logfile_name;
        $this->cp = $cp;
        $this->line = $line;
    }

    public function debug(string $text, ...$params)
    {
        debug_log(["DEBUG",$params],$this->logfile_name,$this->cp,$this->line);
        // TODO: Implement debug() method.
    }
    public function info(string $text, ...$params)
    {
        debug_log(["INFO",$params],$this->logfile_name,$this->cp,$this->line);
        // TODO: Implement info() method.
    }
    public function warn(string $text, ...$params)
    {
        debug_log(["WARN",$params],$this->logfile_name,$this->cp,$this->line);
        // TODO: Implement warn() method.
    }
    public function error(string $text, ...$params)
    {
        debug_log(["ERROR",$params],$this->logfile_name,$this->cp,$this->line);
        // TODO: Implement error() method.
    }
}
class LoggerFacade
{

    public static function getLogger($logfile_name='debug',$cp='',$line='')
    {
        return new logger($logfile_name,$cp,$line);
    }

}

