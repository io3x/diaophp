package com.github.io3x.provider.php.classes;

import com.alibaba.dubbo.config.annotation.Service;
import com.github.io3x.app.func;
import com.github.io3x.provider.php.IPHPService;
import org.noear.snack.ONode;
import org.springframework.stereotype.Component;

import java.util.ArrayList;
import java.util.List;
import java.util.Map;
@Service(interfaceClass = IPHPService.class)
@Component
public class PHPServiceImpl implements IPHPService {
    @Override
    public String m1(int i,String str,List<String> list,List<String> list2, Map<String, Object> map,Object obj) {
        ONode node = new ONode().build((root)->{
            root.getOrNew("php调用int参数示例").val(i);
            root.getOrNew("php调用string参数示例").val(str);
            root.getOrNew("php调用ArrayList参数示例").val(ONode.loadObj(list));
            root.getOrNew("php调用LinkedList参数示例").val(ONode.loadObj(list2));
            root.getOrNew("php调用map参数示例").val(ONode.loadObj(map));
            root.getOrNew("php调用obj参数示例").val(ONode.loadObj(obj));
        });
        String json = node.toJson();
        func.println(json);
        return json;
    }

    @Override
    public String[] m2(String d) {
        func.println(d);
        return new String[]{"a","b","c",d};
    }

    @Override
    public List<String> m3() {
        return new ArrayList<String>(){{
            add("c1");
            add("c2");
            add("c3");
        }};
    }
}
