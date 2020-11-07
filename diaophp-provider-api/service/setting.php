<?php
defined('IN_CDO') or exit('illegal infiltration.');
class loader_provider {
    /**
     * @param $auto_classname
     */
    public static function load_provider_classes($auto_classname){
        $new_ctrls=scan_provider_classes();
        if(isset($new_ctrls[$auto_classname.'.class.php']))  require_once($new_ctrls[$auto_classname.'.class.php']);
    }
}
scan_provider_classes();
spl_autoload_register(array('loader_provider','load_provider_classes'));