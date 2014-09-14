<?php
class TTKService implements ServiceInterface {
    private $tietuku;

    public function __construct($accesskey, $secretkey) {
        $this->tietuku = new MyTieTuKu($accesskey, $secretkey);
    }

    public function formatAlbumName($prefix, $year=null, $month=null) {
        if(is_null($year) && is_null($month)) {
            return $prefix.'_'.date('Ym');
        }else if (is_null($year) xor is_null($month)) {
        	return false;
        }else {
            return $prefix.'_'.$year.$month;
        }
    }
    
    public function getToken($aid, $method) {
    	if($method=='file') {
    		return $this->tietuku->uploadFile($aid);
    	}else if($method=='url') {
    		return $this->tietuku->uploadFromWeb($aid);
    	}
    }
    
        //上传文件
    public function uploadPicByFile($aid, $file,$filename=null) {
        if(is_null($filename)) {
            $filename=basename($file);
        }
        $al = json_decode($this->tietuku->uploadFile($aid, $file, $filename), true);
        if(!isset($al['code']) || $al['code']!=401) {
            return array(
                'orig_url' => $al['linkurl'],
                'thumb_url' => $al['t_url'],
                'show_url' => $al['s_url'],
                'width' => (int)$al['width'],
                'height' => (int)$al['height'],
                'name' => $filename
            );
        }else {
            return false;
        }
    }
    
    public function uploadPicByURL($aid, $url) {
        $al = json_decode($this->tietuku->uploadFromWeb($aid, $url), true);
        if(!isset($al['code']) || $al['code']!=401) {
            return array(
                'orig_url' => $al['linkurl'],
                'thumb_url' => $al['t_url'],
                'show_url' => $al['s_url'],
                'width' => (int)$al['width'],
                'height' => (int)$al['height'],
                'name' => $url
            );
        }else {
            return false;
        }
    }
    
    //获取相册
    public function getAlbums() {
        $albums = array();
        $jalbums = json_decode($this->tietuku->getAlbumByUid(), true);
        foreach($jalbums['album'] as $album) {
            if($album['code'] == 200) {
                $albums[$album['albumname']] = (int)$album['aid'];
            }
        }
        return $albums;
    }
    
    public function getAlbumsYm() {
        $albums = $this->getAlbums();
        $albumsym = array();
        foreach($albums as $name => $id) {
            if(preg_match('/'.ALBUM_PREFIX.'_(\d{4})(\d{2})/', $name, $match)) {
                $albumsym[$match[1]][$match[2]] = $id;
            }
        }
        foreach($albumsym as $year => $whatever) {
            ksort($albumsym[$year]);
        }
        ksort($albumsym);
        return $albumsym;
    }
    
    public function getAlbumByName($name) {
        $albums = $this->getAlbums();
        if(isset($albums[$name])) {
            return $albums[$name];
        }else {
            return false;
        }
    }
    
    //创建相册
    //成功返回相册ID，失败返回false
    public function createAlbum($name) {
        $al = json_decode($this->tietuku->createAlbum($name), true);
        if($al['code'] == 200) {
            return (int)$al['albumid'];
        }else {
            return false;
        }
    }
    
    //修改相册名称
    public function changeAlbumName($aid, $name) {
        $al = json_decode($this->tietuku->editAlbum($aid, $name), true);
        return ($al['code']==200);
    }
    
    //删除相册
    public function deleteAlbum($aid) {
        $al = json_decode($this->tietuku->delAlbum($aid), true);
        return ($al['code']==200);
    }

    //按页码获取相册内的图片
    public function getPicsInAlbum($aid, $page) {
        $pics=array();
        $jpics = json_decode($this->tietuku->getAlbumPicByAid($aid, $page), true);
        foreach($jpics['pic'] as $pic) {
            $thumburl = str_replace($pic['findurl'], $pic['findurl'].'t', $pic['linkurl']);
            $pics[$pic['id']] = array(
                'orig_url' => $pic['linkurl'],
                'thumb_url' => $thumburl,
                'show_url' => $pic['showurl'],
                'width' => (int)$pic['width'],
                'height' => (int)$pic['height'],
                'name' => $pic['name']
            );
        }
        $pics['pages'] = $jpics['pages'];
        return $pics;
    }

}