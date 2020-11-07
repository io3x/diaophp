package com.github.io3x.app.libs.lock;

import com.github.io3x.app.func;

import java.util.Map;
import java.util.concurrent.ConcurrentHashMap;
import java.util.concurrent.atomic.AtomicInteger;

public class internLock {

    public static void lockWait(Runnable r,String... strs){
        StringBuffer sb = new StringBuffer();
        for (String str:strs) {
            sb.append(str);
        }
        synchronized (sb.toString().intern()) {
            func.println(sb.toString());
            new Thread(r).run();
        }
    }
    private static Map<String,AtomicInteger> mapA = new ConcurrentHashMap<>();
    public static void lockLose(Runnable r,String... strs){
        StringBuffer sb = new StringBuffer();
        for (String str:strs) {
            sb.append(str);
        }
        String strKey = sb.toString();
        AtomicInteger ai;
        synchronized (strKey.intern()) {
            if(!mapA.containsKey(strKey)) {
                ai = new AtomicInteger(0);
                mapA.put(strKey,ai);
            } else {
                ai = mapA.get(strKey);
            }
        }
        //incrementAndGet 非线程安全
        ai.incrementAndGet();

        //get 线程安全
        if(ai.get()==1) {
            new Thread(r).run();
            if(mapA.containsKey(strKey)) {
                mapA.remove(strKey);
            }
        }
    }

}
