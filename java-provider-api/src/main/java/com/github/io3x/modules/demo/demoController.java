package com.github.io3x.modules.demo;

import com.github.io3x.app.func;
import com.github.io3x.common.utils.R;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestMethod;
import org.springframework.web.bind.annotation.RestController;

import javax.servlet.http.HttpServletRequest;
import java.util.*;

@RestController
@RequestMapping("/demo/demo")
public class demoController {


    @RequestMapping(value = "/test1",method = {RequestMethod.GET,RequestMethod.POST})
    public R test1(HttpServletRequest request) {
        String ticket = func.randStr(16);
        return R.ok().put(ticket);
    }

    @RequestMapping(value = "/test2",method = {RequestMethod.GET,RequestMethod.POST})
    public R test2(HttpServletRequest request) {
        String ticket = func.randStr(16);
        return R.ok().put(ticket).put("xx",func.json_encode(null));
    }
}
