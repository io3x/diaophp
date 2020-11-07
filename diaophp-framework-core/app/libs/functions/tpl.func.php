<?php
defined('IN_CDO') or exit('illegal infiltration.');
/**
 * 模板调用
 * @param string $module
 * @param string $template
 * @return string
 */
function template_auto($module = 'content', $template = 'index',$fext='html') {
    $module = str_replace('/', DIRECTORY_SEPARATOR, $module);
    $template_cache = new tpl_cache();
    $compiledtplfile = CACHE_PATH.'caches_tpl'.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.$template.'.php';
    /*匹配多级路径*/
    if(!is_dir(dirname($compiledtplfile))) {
        mkdir(dirname($compiledtplfile), 0777, true);
    }
    if(file_exists(ROOT_PATH.$module.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.'.'.$fext)) {
        if(!file_exists($compiledtplfile) || (@filemtime(ROOT_PATH.$module.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.'.'.$fext) > @filemtime($compiledtplfile))) {
            $template_cache->template_compile($module, $template,$fext);
        }
    } else {
        $compiledtplfile = CACHE_PATH.'caches_tpl'.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.$template.'.php';
        if(!file_exists($compiledtplfile) || (file_exists(ROOT_PATH.$module.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.'.'.$fext) && filemtime(ROOT_PATH.$module.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.'.'.$fext) > filemtime($compiledtplfile))) {
            $template_cache->template_compile($module, $template,$fext);
        } elseif (!file_exists(ROOT_PATH.$module.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.'.'.$fext)) {
            exit('no tpl');
        }
    }
    return $compiledtplfile;
}