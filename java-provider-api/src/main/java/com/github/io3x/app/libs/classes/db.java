package com.github.io3x.app.libs.classes;

import com.github.io3x.app.func;
import com.liucf.dbrecord.Db;
import com.liucf.dbrecord.Record;
import org.apache.commons.lang.StringEscapeUtils;
import org.slf4j.LoggerFactory;

import java.util.*;
import java.util.concurrent.ConcurrentHashMap;
import java.util.stream.Collectors;

/**
 * Db
 */
public class db {
    /**
     * Map 2 record record
     *
     * @param map map
     * @return the record
     */
    public static Record map2record(Map map){
        Record record=new Record();
        return record.setColumns(map);
    }

    /**
     * Record 2 map map
     *
     * @param record record
     * @return the map
     */
    public static Map record2map(Record record){
        return record.getColumns();
    }
}
