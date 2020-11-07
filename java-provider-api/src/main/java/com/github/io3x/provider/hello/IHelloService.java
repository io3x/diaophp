package com.github.io3x.provider.hello;

import java.util.Map;

/**
 * The interface Hello service.
 */
public interface IHelloService {
    /**
     * Hello string.
     *
     * @return the string
     */
    public Map<String,Object> hello(String tt);
    public String hello2(String tt);
}
