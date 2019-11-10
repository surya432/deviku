<?php 
class embed{
	private function post_url($url, $body){
	  $ch = @curl_init();
	  curl_setopt($ch, CURLOPT_URL, $url);
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	  if($body != null){
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
	  }
	  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5000);
	  $page = curl_exec($ch);
	  curl_close($ch);
	  return $page;
	}
	private function link_upload($link, $name){
		if (preg_match('@https?://(?:[\w\-]+\.)*(?:drive|docs)\.google\.com/(?:(?:folderview|open|uc)\?(?:[\w\-\%]+=[\w\-\%]*&)*id=|(?:folder|file|document|presentation)/d/|spreadsheet/ccc\?(?:[\w\-\%]+=[\w\-\%]*&)*key=)([\w\-]{28,})@i', $link, $id)) {		
			$links = "http://cdn.dldramaid.xyz:5000/mirror/".$id[1]."/".$name;
		}
		return $links;
    }
	private function rapidvideo($link){
		$link = json_decode($this->post_url($link, null),true);
		if ($link[status] == "OK"){
			$url = $link["sources"][0]["file"];
			$result_curl= $this->post_url("http://api.rapidvideo.com/v1/remote.php?ac=add&user_id=8091&url=".$url, null);
			$result = $this->get_idupload($result_curl);
		}
		return $result;
    }
	private function openload($link){
		$link = json_decode($this->post_url($link, null),true);
		if ($link[status] == "OK"){
			$url = $link["sources"][0]["file"];
			$result_curl= $this->post_url("http://api.openload.co/1/remotedl/add","login=1c6d666055b6a4c0&key=t5EK0UYI&url=".$url);
			$result = $this->get_idupload($result_curl);
		}
		return $result;
    }
	private function get_idupload($result){
		$link = json_decode($result,true);
		if ($link[status] == "200"){
			$result_id="upload_id=".$link[result][id];
		}
		return $result_id;
    }
	public function iframesd($url, $name){
		$url_upload = $this->link_upload($url, $name);
		$url_upload= $this->rapidvideo($url_upload);
		return $url_upload;
	}
	public function iframehd($url, $name){
		$url_upload = $this->link_upload($url, $name);
		$url_upload= $this->openload($url_upload);
		return $url_upload;
	}
	public function check_openload($url){
		$id_upload = str_replace("upload_id=","",$url);
		$result_curl= $this->post_url("http://api.openload.co/1/remotedl/status","login=1c6d666055b6a4c0&key=t5EK0UYI&id=".$id_upload, null);
		$id = json_decode($result_curl,true);
		return $id[result][$id_upload][extid];
	}
	public function check_rapidvideo($url){
		$id_upload = str_replace("upload_id=","",$url);
		$result_curl= $this->post_url("http://api.rapidvideo.com/v1/remote.php?ac=check&user_id=8091&remote_id=".$id_upload, null);
		$id = json_decode($result_curl,true);
		return $id[result][object_code];
	}
}
?>