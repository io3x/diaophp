package com.github.io3x.provider.hello.classes;

import com.alibaba.dubbo.config.annotation.Service;
import com.github.io3x.app.func;
import com.github.io3x.provider.hello.IHelloService;
import org.springframework.stereotype.Component;

import java.util.HashMap;
import java.util.Map;

@Service(interfaceClass = IHelloService.class)
@Component
public class HelloServiceImpl implements IHelloService {
    @Override
    public Map<String,Object> hello(String tt) {
        Map<String,Object> map = new HashMap<>();
        map.put("getenv",System.getenv());
        map.put("getProperties",System.getProperties());
        map.put("date",func.getCurdatetime());
        map.put("tt",tt);
        return map;
    }

    @Override
    public String hello2(String tt) {
        Map<String,Object> map = new HashMap<>();
        map.put("getenv",System.getenv());
        map.put("getProperties",System.getProperties());
        map.put("date",func.getCurdatetime());
        map.put("tt",tt);
        return func.json_encode(map);
    }
}
