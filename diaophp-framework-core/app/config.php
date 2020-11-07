<?php
defined('IN_CDO') or exit('illegal infiltration.');
$hostname = gethostname();
$host_config = DIAOPHP_FRAMEWORK_CORE_PATH."config".DIRECTORY_SEPARATOR.$hostname.".php";
if(file_exists($host_config)) {
    return include_once $host_config;
} else {
    return include_once DIAOPHP_FRAMEWORK_CORE_PATH."config".DIRECTORY_SEPARATOR."default.php";
}
