<?php

// Setup path for upload
function set_album() {
	switch ALBUM_STRATEGY {
		case 'monthly':
    		$album_name=Service::srv()->formatAlbumName(ALBUM_PREFIX);
    		$id = Service::srv()->getAlbumByName($album_name);
    		if($id===false) {
        		$id = Service::srv()->createAlbum($album_name);
    		}
    		break;
    	case 'single':
    		$id = SINGLE_ALBUM;
    		break;
    }
    return $id;
}

function get_token($method) {
	$aid = set_album();
	return array('token'=>Service::srv()->getToken($aid, $method));
}

function set_pic_size($width_orig, $height_orig) {
    $height = 200;
    $width = 1000;
    if($height_orig <= $height && $width_orig <= $width) {
        $return['width'] = $width_orig;
        $return['height'] = $height_orig;
    }else{
        $ratio_orig = $width_orig/$height_orig;
        if ($width/$height > $ratio_orig) {
            $width = $height*$ratio_orig;
        }else {
            $height = $width/$ratio_orig;
        }
        $return['width'] = $width;
		$return['height'] = $height;
    }
    return $return;
}

function url_handler() {
    $aid = set_album();
    $url = $_POST['url'];
    $qid = $_POST['qid'];
    if(!preg_match('/^\s*https?:\/\/.+$/', $url)) {
        return array('status' => 'failed', 'err' => 'wrong_type');
    }
    $pic = Service::srv()->uploadPicByURL($aid, $url);
    if($pic === false) {
        return array('status' => 'failed', 'err' => 'write_prohibited');
    }else {
        $pic_size = set_pic_size($pic['width'], $pic['height']);
        return array(
            'qid'=> $qid,
            'status' => 'success',
            'path' => $pic['orig_url'],
            'thumb' => $pic['thumb_url'],
            'name' => $pic['name'],
            'width' => $pic_size['width'],
            'height' => $pic_size['height']
        );
    }
}

function file_handler() {
    $aid = set_album();
    $files = $_FILES['files'];
    $results=array();
    
    foreach($files['error'] as $key => $error) {
        $qid=isset($_POST['qid'])?$_POST['qid']:0;
        $filename = $files['name'][$key];
        
        if($error==UPLOAD_ERR_OK) {
            $temp = $files['tmp_name'][$key];
            $pic = Service::srv()->uploadPicByFile($aid, $temp, $filename);
            if($pic === false) {
                $result = array('status' => 'failed', 'err' => 'write_prohibited');
            }else {
                $pic_size = set_pic_size($pic['width'], $pic['height']);
                $result = array(
                    'qid'=>$qid,
                    'status' => 'success',
                    'path' => $pic['orig_url'],
                   'thumb' => $pic['thumb_url'],
                   'name' => $filename,
                   'width' => $pic_size['width'],
                    'height' => $pic_size['height']
                );
            }
        }else {
            $result=array('qid'=>$qid);
            switch($error) {
				case UPLOAD_ERR_INI_SIZE:
					$result['status'] = 'failed';
					$result['err'] = 'php_upload_size_limit';
					break;
				case UPLOAD_ERR_FORM_SIZE:
					$result['status'] = 'failed';
					$result['err'] = 'size_limit';
					break;
				case UPLOAD_ERR_PARTIAL:
					$result['status'] = 'failed';
					$result['err'] = 'part_upload';
					break;
				case UPLOAD_ERR_NO_FILE:
					$result['status'] = 'failed';
					$result['err'] = 'no_file';
					break;
				case UPLOAD_ERR_NO_TMP_DIR:
					$result['status'] = 'failed';
					$result['err'] = 'no_tmp';
					break;
				case UPLOAD_ERR_CANT_WRITE:
					$result['status'] = 'failed';
					$result['err'] = 'write_prohibited';
					break;
			}
        }
        array_push($results, $result);
    }
    return $results;
}

function file_mime_type($file) {
	if(function_exists('mime_content_type')) {
		return mime_content_type($file);
	}elseif(function_exists('finfo_open') && ($finfo=finfo_open(FILEINFO_MIME_TYPE))) {
		return finfo_file($finfo, $file);
	}elseif(function_exists('fopen') && ($hl=fopen($file, 'r'))) {
		$bytes = fread($hl, 512);
		if(preg_match('/^\x89\x50\x4e\x47\x0d\x0a\x1a\x0a/',$bytes)) {
			return 'image/png';
		}elseif(preg_match('/^\xff\xd8/',$bytes)) {
			return 'image/jpeg';
		}elseif(preg_match('/^GIF8/',$bytes)) {
			return 'image/gif';
		}elseif(preg_match('/^BM....\x00\x00\x00\x00/',$bytes)) {
			return 'image/bmp';
		}elseif(preg_match('/^\s*<\?xml\C+<!DOCTYPE svg/',$bytes)) {
			return 'image/svg+xml';
		}else {
			return 'unknow';
		}
		fclose($hl);
	}
	return false;
}