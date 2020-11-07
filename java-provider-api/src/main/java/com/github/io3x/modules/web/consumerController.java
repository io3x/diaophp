package com.github.io3x.modules.web;

import com.alibaba.dubbo.config.annotation.Reference;
import com.github.io3x.app.func;
import com.github.io3x.common.utils.R;
import com.github.io3x.provider.hello.IHelloService;
import com.github.io3x.provider.php.IPHPService;
import com.github.io3x.provider.php.ProviderService;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestMethod;
import org.springframework.web.bind.annotation.RestController;
import com.github.io3x.php.ip_service;

import java.util.HashMap;
import java.util.Map;

@RestController
@RequestMapping("/dubbo/java/")
public class consumerController {

    @Reference
    private ProviderService providerSer;

    @Reference
    private IPHPService phpSer;

    @Reference
    private IHelloService helloSer;

    @Reference
    private ip_service demo2;

    @RequestMapping(value = "/sayHello",method = {RequestMethod.GET,RequestMethod.POST})
    public R sayHello() {
        String ticket = func.randStr(16);
        return R.ok().put(ticket).put("r",new HashMap<String,Object>() {
            {
                put("IHelloService",helloSer.hello(func.getCurdatetime()));
                put("ProviderService",providerSer.sayHello());
            }
        });
    }



}
