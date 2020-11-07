package com.github.io3x.modules.demo.classes;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.stereotype.Component;

import java.util.concurrent.atomic.AtomicInteger;

/**
 * Bulksend event
 */
@Component
public class bulksendEvent {
    Logger logger = LoggerFactory.getLogger(this.getClass());


    /**
     * A integer 只想给当前用的原子计数器
     */
    public class AInteger{
        /*全局线程安全计数器*/
        public AtomicInteger atomiCount = new AtomicInteger(0);
        public AtomicInteger getAtomci(){
            if (atomiCount==null) {
                atomiCount = new AtomicInteger(0);
            }
            return atomiCount;
        }
    }


}
