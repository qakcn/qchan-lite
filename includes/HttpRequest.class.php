<?php
class HTTPRequest {

    private $method;
    private $host;
    private $port;
    private $path;
    private $query='';
    private $header=array();
    private $posts=array();
    private $files=array();
    private $multipart = false;
    
    private $postdata='';
    private $headerdata='';
    
    const HTTP_EOL = "\r\n";
    
    public function __construct() {
        $this->setHeader(array(
            array('name'=>'User-Agent', 'value'=>'HTTPRequest/1.0 (PHP; author/qakcn)'),
            array('name'=>'Accept', 'value'=>'*/*'),
            array('name'=>'Connection', 'value'=>'Close'),
            array('name'=>'Cache-Control', 'value'=>'no-cache'),
        ));
        $this->setMethod('get');
        $this->setURL('http://localhost/');
    }
    
    public function setURL($url) {
        $url = parse_url($url);
        if(isset($url['scheme']) && $url['scheme'] == 'http') {
            $this->host = $url['host'];
            $this->port = isset($url['port']) ? $url['port'] : 80;
            $this->path = isset($url['path']) ? $url['path'] : '/';
            $this->query = isset($url['query']) ? '?'.$url['query'] : '';
        }else {
            return false;
        }
    }
    
    public function setMethod($method) {
        if(strcasecmp($method, "post") == 0 || strcasecmp($method, "get") == 0) {
            $this->method = strtoupper($method);
        }else if(strcasecmp($method, "multipart") == 0) {
            $this->method = 'POST';
            $this->multipart = true;
        }else {
            return false;
        }
    }
    
    public function send() {
        $fp = fsockopen($this->host, $this->port);
        if(!$fp) return false;
        $this->genPostdata();
        $this->genHeader();
        fwrite($fp, $this->headerdata.$this->postdata);
        $result = '';
        while(!feof($fp)) {
            $result .= fgets($fp, 1024);
        }
        fclose($fp);
        $pos = strpos($result, "\r\n\r\n");
        $result = substr($result, $pos+4);
        
        return $result;
    }
    
    public function addQuery(array $query) {
        if(isset($query['key']) && isset($query['value'])) {
            array_push($this->posts, $query);
        }else {
            foreach($query as $q) {
                if(isset($q['key']) && isset($q['value'])) {
                    array_push($this->posts, $q);
                }
            }
        }
    }
    
    public function setHeader(array $header) {
        if(isset($header['name']) && isset($header['value'])) {
            array_push($this->header, $header);
        }else {
            foreach($header as $h) {
                if(isset($h['name']) && isset($h['value'])) {
                    array_push($this->header, $h);
                }
            }
        }
    }
    
    public function addFile(array $file) {
        if(isset($file['name']) && isset($file['path']) && isset($file['filename'])) {
            array_push($this->files, $file);
        }else {
            foreach($file as $f) {
                if(isset($f['name']) && isset($f['path']) && isset($file['filename'])) {
                    array_push($this->files, $f);
                }
            }
        }
    }
    
    private function genPostdata() {
        if(count($this->files)==0 && !$this->multipart) {
            if(count($this->posts)!=0) {
                $query = '';
                foreach($this->posts as $post) {
                    $query .= rawurlencode($post['key']).'='.rawurlencode($post['value']).'&';
                }
                $query = substr($query,0,-1);
                if($this->method=='POST') {
                    $this->setHeader(array('name'=>'Content-Type','value'=>'application/x-www-form-urlencoded'));
                    $this->postdata = $query;
                }else {
                    if($this->query=='') {
                        $this->query = '?'.$query;
                    }else {
                        $this->query .= '&'.$query;
                    }
                }
            }
        }else {
            // 设置分割标识
            srand((double)microtime()*1000000);
            $boundary = '---------------------------'.substr(md5(rand(0,32000)),0,10);
            
            $this->setHeader(array('name'=>'Content-Type','value'=>'multipart/form-data; boundary='.$boundary));
            $this->postdata = '--'.$boundary.HTTPRequest::HTTP_EOL;
            
            if(count($this->posts)!=0) {
                foreach($this->posts as $post) {
                    $this->postdata .= 'Content-Disposition: form-data; name="'.$post['key'].'"'.HTTPRequest::HTTP_EOL.HTTPRequest::HTTP_EOL;
                    $this->postdata .= $post['value'].HTTPRequest::HTTP_EOL;
                    $this->postdata .= '--'.$boundary.HTTPRequest::HTTP_EOL;
                }
            }
            foreach($this->files as $file) {
                if(file_exists($file['path'])) {
                    $this->postdata .= 'Content-Disposition: form-data; name="'.$file['name'].'"; filename="'.$file['filename'].'"'.HTTPRequest::HTTP_EOL;
                    $mime = file_mime_type($file['path']);
                    if($mime) {
                        $this->postdata .= 'Content-Type: '.$mime.HTTPRequest::HTTP_EOL;
                    }
                    $this->postdata .= HTTPRequest::HTTP_EOL;
                    $contents = file_get_contents($file['path']);
                    $this->postdata .= $contents.HTTPRequest::HTTP_EOL;
                    $this->postdata .= '--'.$boundary.HTTPRequest::HTTP_EOL;
                }
            }
            $this->postdata = substr($this->postdata, 0, -2).'--'.HTTPRequest::HTTP_EOL;
            $this->setHeader(array('name'=>'Content-Length', 'value'=>strlen($this->postdata)));
        }
    }
    
    private function genHeader() {
        $this->headerdata = $this->method.' '.$this->path.$this->query.' HTTP/1.1'.HTTPRequest::HTTP_EOL;
        $this->headerdata .= 'Host: '.$this->host.HTTPRequest::HTTP_EOL;
        foreach($this->header as $h) {
            $this->headerdata.= $h['name'].': '.$h['value'].HTTPRequest::HTTP_EOL;
        }
        $this->headerdata .= HTTPRequest::HTTP_EOL;
    }
}