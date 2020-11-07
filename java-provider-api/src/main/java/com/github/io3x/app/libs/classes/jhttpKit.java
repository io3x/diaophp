package com.github.io3x.app.libs.classes;

import com.jianggujin.http.core.JHttpException;
import com.jianggujin.http.core.JMethod;
import com.jianggujin.http.core.JRequest;
import com.jianggujin.http.core.JResponse;
import com.jianggujin.http.response.JFileResponse;
import com.jianggujin.http.response.JTextResponse;

import java.io.File;
import java.io.IOException;
import java.io.InputStream;
import java.nio.file.Files;
import java.nio.file.StandardOpenOption;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.UUID;
import java.util.concurrent.ConcurrentHashMap;
import java.util.concurrent.CyclicBarrier;

/**
 * Jhttp kit
 */
public class jhttpKit {
    /**
     * Main
     *
     * @param args args
     * @throws IOException io exception
     */
    public static void main(String[] args) throws IOException {
    }

    public static String get(String url){
        try {
            JResponse response = new JTextResponse();
            JRequest.create(url)
                    .method(JMethod.GET)
                    .timeout(3000)
                    .response(response)
                    .execute();
            return response.getData().toString();
        } catch (Exception e) {
            return e.toString();
        }
    }

    /**
     * Get string
     *
     * @param url     url
     * @param headers headers
     * @return the string
     */
    public static String get(String url,Map<String, String> headers){
        try {
            JResponse response = new JTextResponse();
            JRequest.create(url)
                    .method(JMethod.GET)
                    .timeout(3000)
                    .response(response)
                    .header(headers)
                    .execute();
            return response.getData().toString();
        } catch (JHttpException e) {
            e.printStackTrace();
            return e.toString();
        }
    }

    /**
     * 下载远程文件到本地 Remote url 2 file file
     *
     * @param url url
     * @return the file
     */
    public static File getRemotefile(String url, String basePath){
        try {
            String createURL;
            if(url.startsWith("//")) {
                createURL = "https:"+url;
            } else {
                createURL = url;
            }
            String filename = UUID.randomUUID().toString();
            File tmpF = new File(basePath,filename+"."+"tmp");
            JResponse response = new JFileResponse(tmpF);
            JRequest.create(createURL)
                    .method(JMethod.GET)
                    //13S 下载超时
                    .timeout(13000)
                    .response(response)
                    .header(new HashMap<String, String>(){{
                        put("Referer",url);
                        put("User-Agent","Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36 "+UUID.randomUUID().toString());
                    }})
                    .execute();
            String ContentType = response.getHeaders().get("Content-Type").toString();
            //String fExt = ContentType.split("\\/")[1].replaceAll("[\\[\\]]","");
            String fExt;
            String ct = new String(ContentType).toLowerCase();

            if(ct.contains("jpeg")) {
                fExt = "jpg";
            } else if(ct.contains("jpg")){
                fExt = "jpg";
            } else if(ct.contains("png")){
                fExt = "png";
            } else if(ct.contains("gif")){
                fExt = "gif";
            } else if(ct.contains("bmp")){
                fExt = "bmp";
            }  else {
                fExt = "webp";
            }

            /*重命名保存在新位置*/
            File tmpF2 = new File(basePath,filename+"."+fExt);
            tmpF.renameTo(tmpF2);

            return tmpF2;
        } catch (JHttpException e) {
            e.printStackTrace();
            return null;
        }
    }

    /**
     * Post string
     *
     * @param url     url
     * @param d       d
     * @param headers headers
     * @return the string
     */
    public static String post(String url,Map<String, String> d,Map<String, String> headers){
        try {
            JResponse response = new JTextResponse();
            JRequest jreq = JRequest.create(url)
                    .method(JMethod.POST)
                    .data(d)
                    .timeout(3000)
                    .response(response)
                    //.header("Content-Type", "application/json")
                    .header(headers)
                    .execute();
            return response.getData().toString();
        } catch (JHttpException e) {
            return e.toString();
        }
    }

    /**
     * Post 2 string
     *
     * @param url     url
     * @param body    body
     * @param headers headers
     * @return the string
     */
    public static String post2(String url,String body,Map<String, String> headers){
        try {
            JResponse response = new JTextResponse();
            JRequest.create(url)
                    .method(JMethod.POST)
                    .requestBody(body)
                    .timeout(3000)
                    .response(response)
                    .header(headers)
                    .execute();
            return response.getData().toString();
        } catch (JHttpException e) {
            return e.toString();
        }
    }

    public static String post(String url,Map<String, String> d){
        try {
            JResponse response = new JTextResponse();
            JRequest.create(url)
                    .method(JMethod.POST)
                    .data(d)
                    .timeout(6000)
                    .response(response)
                    .execute();
            return response.getData().toString();
        } catch (JHttpException e) {
            return e.toString();
        }
    }

    /**
     * Post file string 单个上传文件
     *
     * @param url       url
     * @param formField form field
     * @param filePath  file path
     * @param headers   headers
     * @return the string
     */
    public static String postFile(String url,String formField,String filePath,Map<String, String> headers){
        File f = new File(filePath);
        try {
            InputStream in = Files.newInputStream(f.toPath(), StandardOpenOption.READ);
            JResponse response = new JTextResponse();
            JRequest jreq = JRequest.create(url)
                    .method(JMethod.POST)
                    .data(formField,f.getName(),in)
                    .timeout(10000)
                    .response(response)
                    //.header("Content-Type", "application/json")
                    .header(headers)
                    .execute();
            in.close();
            return response.getData().toString();
        } catch (Exception e) {
            return null;
        }
    }

    /**
     * More post file map 多线程批量上传文件
     *
     * @param url       url
     * @param formField form field
     * @param filePaths file paths
     * @param headers   headers
     * @return the map
     */
    public static Map<String,String> morePostFile(String url, String formField, List<String> filePaths, Map<String, String> headers){
        int barrierCount = filePaths.size()+1;
        Map<String,String> a = new ConcurrentHashMap<String, String>();
        if(barrierCount>0) {
            final CyclicBarrier barrier = new CyclicBarrier(barrierCount);

            filePaths.forEach(item->{
                try {
                    new Thread(()->{
                        String img = postFile(url,formField,item,headers);
                        if(img==null) {
                            img = postFile(url,formField,item,headers);
                        }
                        a.put(item,String.valueOf(img));
                        try {
                            barrier.await();
                        } catch (Exception e) {
                            e.printStackTrace();
                        }
                    }).start();
                } catch (Exception e) {
                    e.printStackTrace();
                }
            });
            try {
                barrier.await();
            } catch (Exception e) {
                e.printStackTrace();
            }

            /*a.forEach((key,value)->{
                data.put(key,value);
            });*/

        }
        return a;
    }


    /**
     * Put string
     *
     * @param url     url
     * @param body    body
     * @param headers headers
     * @return the string
     */
    public static String put(String url,String body,Map<String, String> headers){
        try {
            JResponse response = new JTextResponse();
            JRequest.create(url)
                    .method(JMethod.PUT)
                    .requestBody(body)
                    .timeout(3000)
                    .response(response)
                    //.header("Content-Type", "application/json")
                    .header(headers)
                    .execute();
            return response.getData().toString();
        } catch (Exception e) {
            return e.toString();
        }
    }
}
