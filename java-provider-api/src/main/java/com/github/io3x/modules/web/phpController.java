package com.github.io3x.modules.web;

import com.alibaba.dubbo.config.annotation.Reference;
import com.github.io3x.app.func;
import com.github.io3x.provider.php.ProviderService;
import com.github.io3x.php.shop_service;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestMethod;
import org.springframework.web.bind.annotation.RestController;
import com.github.io3x.php.ip_service;

@RestController
@RequestMapping("/dubbo/php/")
public class phpController {

    @Reference
    private ProviderService providerService;

    @Reference
    private ip_service ipSer;

    @Reference
    private shop_service shopSer;

    @RequestMapping(value = "/sayHello",method = {RequestMethod.GET,RequestMethod.POST})
    public String sayHello() {
        return providerService.sayHello();
    }

    @RequestMapping(value = "/swoole_get_local_ip",method = {RequestMethod.GET,RequestMethod.POST})
    public String ip() {
        return ipSer.swoole_get_local_ip();
    }

    @RequestMapping(value = "/shop_m1",method = {RequestMethod.GET,RequestMethod.POST})
    public String shop_m1() {
        return shopSer.m1(func.getCurdatetime(),func.randStr(6),func.randStr(8));
    }

    @RequestMapping(value = "/shop_async_m1",method = {RequestMethod.GET,RequestMethod.POST})
    public String async_m1() {
        return shopSer.async_m1(func.getCurdatetime(),func.randStr(6),func.randStr(8));
    }
}
