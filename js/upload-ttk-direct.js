var qchan=function(){
	
	// private properties
	var that = {};
	var upload_count = 3;
	
	// static properties
	qchan.prototype.queue = qchan.prototype.queue || [];
	qchan.prototype.working = qchan.prototype.working || 0;
	qchan.prototype.queueid = qchan.prototype.queueid || 0;
	qchan.prototype.before = qchan.prototype.before || function(){};
	qchan.prototype.after = qchan.prototype.after || function(){};
	qchan.prototype.progress = qchan.prototype.progress || function(){};
	
	// private methods
	var getQID = function() {
		return qchan.prototype.queueid++;
	};
	
	var getWorking = function() {
		return qchan.prototype.working;
	};
	
	var incWorking = function() {
		qchan.prototype.working++;
	};
	
	var decWorking = function() {
		qchan.prototype.working--;
	};
	
	var pushQueue = function(work) {
		qchan.prototype.queue.push(work);
	};
	var getQueueLength = function() {
		return qchan.prototype.queue.length;
	};
	var shiftQueue = function() {
		return qchan.prototype.queue.shift();
	}
	
	var isurl = function(theurl) {
		return /^\s*https?:\/\/.+$/.test(theurl);
	}

	var isempty = function(theurl) {
		return (/^\s*$/.test(theurl) || theurl=='');
	}
	
	var callBefore = function(work) {
		qchan.prototype.before(work);
	};
	var callAfter = function(res) {
		qchan.prototype.after(res);
	};
	var callProgress = function(work, range) {
		qchan.prototype.progress(work, range);
	};
	
	
	var retry_upload = function(work) {
		decWorking();
		work.retry++;
		if(work.retry < 3) {
			upload(work);
		}else {
			work.status = 'failed';
			work.err = 'fail_retry';
		}
	};
	
	var upload_next = function() {
		decWorking();
		if(getQueueLength() > 0) {
			upload(shiftQueue());
		}
	};
	
	var get_token = function(method, callback) {
		var xhr = new XMLHttpRequest();
	
		xhr.open('GET', 'api.php?gettoken='+method, true);
	
		xhr.addEventListener('readystatechange', (function(callback){
			return function(e) {
				if(xhr.readyState == 4) {
					if(xhr.status == 200) {
						var res=JSON.parse(xhr.responseText);
						callback(res.token);
					}
				}
			}
		})(callback),false);
	
		xhr.send();
	};
	
	var set_pic_size = function(width_orig, height_orig) {
		height = 200;
		width = 1000;
		re={};
		if(height_orig <= height && width_orig <= width) {
			re.width = width_orig;
			re.height = height_orig;
		}else{
			ratio_orig = width_orig/height_orig;
			if (width/height > ratio_orig) {
				width = height*ratio_orig;
			}else {
				height = width/ratio_orig;
			}
			re.width = width;
			re.height = height;
		}
		return re;
	};
	
	var upload = function(work) {
		get_token(work.type, (function(work){
			return function(ttktoken){
				var xhr = new XMLHttpRequest();
				var fd = new FormData();

				xhr.open('POST', 'http://up.tietuku.com/', true);
				//xhr.setRequestHeader("Content-type", "multipart/form-data");

				xhr.addEventListener('readystatechange', (function(qid){
					return function(e) {
						if(xhr.readyState == 4) {
							if(xhr.status == 200) {
								var res=JSON.parse(xhr.responseText);
								var pic_size=set_pic_size(res.width, res.height);
								if(!res.code)
									fin= {
										'qid': qid,
										'status': 'success',
										'path': res.linkurl,
										'thumb': res.t_url,
										'name': res.name,
										'width': pic_size.width,
										'height': pic_size.height
									};
								callAfter(fin);
								upload_next();
							}
						}
					}
				})(work.qid),false);
				
				var progress_callback = (function(work){
					return function(range){
						callProgress(work, range);
					}
				})(work);

				xhr.upload.addEventListener('progress',function(e){
					if(e.lengthComputable) {
						var percentage = e.loaded/e.total;
						progress_callback(percentage);
					}
				},false);
				xhr.upload.addEventListener('load',function(){
					progress_callback(1);
				},false);

				fd.append('Token',ttktoken);
				if(work.type == 'file') {
					fd.append('file',work.fileobj);
				}else if(work.type == 'url') {
					fd.append('fileurl',work.path);
				}

				xhr.send(fd);
			}
		})(work));
	};
	
	that.isSupport = !!window.FormData;
	
	that.setBefore = function(before) {
		qchan.prototype.before = before;
	};
	
	that.setAfter = function(after) {
		qchan.prototype.after = after;
	};
	
	that.setProgress = function(progress) {
		qchan.prototype.progress = progress;
	};
	
	// public method
	that.url_upload = function(urls) {
		for (var url,i=0;i<urls.length;i++) {
			url = urls[i].trim();
			var work = {
				qid: getQID(),
				type: 'url',
				path: url,
				retry: 0
			};
			if(!isempty(url) || !isurl(url)) {
				work.status = 'failed';
				work.err = 'illegal_url';
			}else {
				work.status = 'prepared';
			}
			callBefore(work);
			upload(work);
		}
	};
	
	that.file_upload = function(filelist) {
		for(var file,i=0;i<filelist.length;i++) {
			file=filelist[i];
			var work = {
				qid: getQID(),
				type: 'file',
				path: file.name,
				fileobj: file
			};
			if(!/image\/(jpeg|png|gif|svg\+xml)/.test(file.type)) {
				work.status = 'failed';
				work.err = 'wrong_type';
			}else if(file.size > prop.size_limit) {
				work.status = 'failed';
				work.err = 'size_limit';
			}else {
				work.status = 'prepared';
			}
			callBefore(work);
			upload(work);
		}
	};
	
	that.form_upload = function(formele) {
		formele.method = 'post';
		formele.action = 'index.php';
		formele.submit();
	}
	
	return that;
};