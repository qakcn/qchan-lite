<?php
class MyTieTuKu extends TTKClient{
    
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