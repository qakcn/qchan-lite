<?php
interface ServiceInterface {
    
    //上传文件
    public function uploadPicByFile($aid, $file);
    public function uploadPicByURL($aid, $url);
    
    //获取相册
    public function getAlbums();
    
    //按年月来返回相册
    public function getAlbumsYm();
    
    //以名称获取相册
    //成功返回ID，失败返回false
    public function getAlbumByName($name);
    
    //创建相册
    //成功返回相册ID，失败返回false
    public function createAlbum($name);
    
    //修改相册名称
    public function changeAlbumName($aid, $name);
    
    //删除相册
    public function deleteAlbum($aid);

    //按页码获取相册内的图片
    public function getPicsInAlbum($aid, $page);

    //获取相册名的date函数格式化字符串
    public function formatAlbumName($prefix, $year=null, $month=null);
    
    public function getToken($aid, $method);
}