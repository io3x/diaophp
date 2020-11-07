package com.github.io3x.modules.web;

import com.alibaba.dubbo.config.annotation.Reference;
import com.github.io3x.app.func;
import com.github.io3x.common.utils.R;
import com.github.io3x.provider.hello.IHelloService;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestMethod;
import org.springframework.web.bind.annotation.RestController;

import javax.servlet.http.HttpServletRequest;
import java.util.*;

@RestController
@RequestMapping("/web/index")
public class indexController {

    @Reference
    private IHelloService iHelloService;


    @RequestMapping(value = "/init",method = {RequestMethod.GET,RequestMethod.POST})
    public String init(HttpServletRequest request) {
        String ticket = func.randStr(16);
        Map<String,Object> a = iHelloService.hello(ticket);
        return func.json_encode(R.ok().put(a));
    }
}
