package com.github.io3x.provider.php.classes;

import com.github.io3x.provider.php.ProviderService;
import org.springframework.stereotype.Component;

import com.alibaba.dubbo.config.annotation.Service;

@Service(interfaceClass = ProviderService.class)
@Component
public class ProviderServiceImpl implements ProviderService{

    @Override
    public String sayHello() {
        return "hello!!!";
    }

}