<?php
/* Management Functions */
function is_login() {
	return (isset($_COOKIE['login_name']) && $_COOKIE['login_name']==hash('sha256',MANAGE_NAME));
}

function check_login(&$error) {
	if(!isset($_POST['login'])) {
		$error=1; //No Login Name
		return false;
	}else if(!isset($_POST['password'])) {
		$error=2; //No Password
		return false;
	}else if($_POST['login']!=MANAGE_NAME) {
		$error=3; // Login Name Incorrect
		return false;
	}else if($_POST['password']!=MANAGE_PASSWORD) {
		$error=4; //Password Incorrect
		return false;
	}else {
		return true;
	}
}

function set_login() {
	setcookie('login_name', hash('sha256',MANAGE_NAME), time()+3000);
}

function list_dir(){
    $albums = Service::srv()->getAlbumsYm();
    
    foreach ($albums as $year => $album) {
        if(isset($_GET['year']) && $_GET['year']==$year) {
			echo '<li class="chosen">' . $year . '<ul>';
		}else {
			echo '<li>' . $year . '<ul>';
		}
		foreach($album as $month => $aid) {
		    if(isset($_GET['year']) && isset($_GET['month']) && $_GET['year']==$year && $_GET['month']==$month) {
				echo '<li class="chosen"><a href="?year=' . $year . '&month=' . $month . '">' . $month . '</a></li>';
			}else {
				echo '<li><a href="?year=' . $year . '&month=' . $month . '">' . $month . '</a></li>';
			}
		}
		echo '</ul>';
    }
}

function get_files($page=1) {
	if(isset($_GET['year']) && isset($_GET['month'])) {
		$year=$_GET['year'];
		$month=$_GET['month'];
		$aid=Service::srv()->getAlbumByName(Service::srv()->formatAlbumName($year,$month));
		return Service::srv()->getPicsInAlbum($aid,$page);
	}
}

function get_album_url() {
    if(isset($_GET['year']) && isset($_GET['month'])) {
		$year=$_GET['year'];
		$month=$_GET['month'];
		$aid=Service::srv()->getAlbumByName(Service::srv()->formatAlbumName($year,$month));
        return 'http://tietuku.com/album/'.$aid;
    }else {
        return '';
    }
}

function format_filelist($filem) {
	if(!$filem) return '';
	$format = <<<FORMAT
<li class="scroll-load" id="n%d" draggable="true" style="width: %dpx; height: %dpx; margin-top: %dpx;" data-path="%s" data-thumb="%s"><div class="img" style="background-image: url(&quot;images/none.svg&quot;); background-size: %dpx %dpx; width: %dpx; height: %dpx;"><div><div class="select" style="padding-top: %dpx;"><p>%s</p></div></div></div></li>
FORMAT;
	$output='';
	$select=__('Selected');
	foreach($filem as $id => $file) {
	    if($id != 'pages') {
	        $size=set_pic_size($file['width'], $file['height']);
	        $output .= sprintf($format, $id, $size['width'], $size['height'], (205-$size['height']), $file['orig_url'], $file['thumb_url'], $size['width'], $size['height'], $size['width'], $size['height'], $size['height']-30, $select);
	    }
	}
	return $output;
}

function format_script($filem) {
	if(!$filem) return '';
	$format = <<<FORMAT
if(!n%d) {
	n%d = document.getElementById('n%d');
}
n%d.onclick = toggleinfo();
n%d.ondblclick = openimage;
n%d.oncontextmenu = toggleinfo();
n%d.work = %s;

FORMAT;
	$output = '';
	foreach($filem as $id => $file) {
	    if($id != 'pages') {
	        $size=set_pic_size($file['width'], $file['height']);
	        $work = json_encode(array('name'=>$file['name'], 'path'=>$file['orig_url'], 'thumb' => $file['thumb_url'], 'qid' => 'n'.$id));
	        $output .= sprintf($format, $id, $id, $id, $id, $id, $id, $id, $work);
	    }
	}
	return $output;
}

function delete_files($works) {
	$result = array();
	foreach($works as $work) {
	        $pid=substr($work['qid'], 1);
	        $result[$work['qid']]='failed';
	}
	return $result;
}

