<?php
class MyTieTuKu extends TTKClient{
    
    function uploadFile($aid,$file,$filename) {
        $url = $this->upload_host;
		$param['deadline'] = time()+$this->timeout;
		$param['aid'] = $aid;
		$param['from'] = 'file';
		$Token=$this->op_Token->dealParam($param)->createToken();
		$data['Token']=$Token;
		$data['file']=array('path'=>$file,'filename'=>$filename);
		return empty($file)?$Token:$this->post($url,$data);
    }
    
    // Rewrite post method for not using curl
    function post($url,$post_data){
        $hr = new HttpRequest();
        $hr->setURL($url);
        $hr->setMethod('multipart');
        foreach($post_data as $name => $value){
            if($name == 'file') {
                $hr->addFile(array('name'=>'file','path'=>$value['path'], 'filename'=>$value['filename']));
            }else {
                $hr->addQuery(array('key'=>$name, 'value'=>$value));
            }
        }
        $result = $hr->send();
        return $result;
    }
}