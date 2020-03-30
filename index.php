<?php
//<-------网站配置开始------->
//网站标题
$title = "Hostloc";
//十八禁目标地址
$index_go = "https://saikou.net";
//网站域名
$domain = $_SERVER['HTTP_HOST'];
//<-------网站配置结束------->
if(!file_exists(__DIR__.'/page.base.php')){
    exit('[ERROR:1000]页面文件无法找到');
}
if(!file_exists(__DIR__.'/cache.dat')){
    if(!touch(__DIR__.'/cache.dat')){
        exit('[ERROR:1001]无法创建Cache文件,请确保index.php所在目录可以被读写!');
    }
}
$CacheData = json_decode(@file_get_contents(__DIR__.'/cache.dat'),true);
$ReportData = array('title' => $title,'domain' => $domain,'version' => '1');
if((int)(@$CacheData['cache_end_time']) < time() || empty(@$CacheData['cache_info'])){
    //缓存需要被刷新
    $CacheGet = _curl('https://da.jiba.show/api.php','POST',$ReportData);
    if(!$CacheGet['success']){
        if(!isset($CacheData['cache_info'])){
            exit('[ERROR:1002]信息获取失败,请稍后重试:'.$CacheGet['data']);
        }else{
            $EchoLinks = $CacheData['cache_info'];
        }
    }else{
        $EchoLinks = $CacheGet['data'];
        file_put_contents(__DIR__.'/cache.dat',json_encode(array('cache_end_time' => (time() + 1000),'cache_info' => $EchoLinks)));
    }
}else{
    $EchoLinks = $CacheData['cache_info'];
}
require __DIR__.'/page.base.php';
function _curl($url,$method,$query){
	$ch = curl_init();
	if($method == 'POST'){
		curl_setopt($ch, CURLOPT_URL, $url);
	}else{
		curl_setopt($ch, CURLOPT_URL, $url.'?'.http_build_query($query));
	}
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	//Fix
	curl_setopt($ch,CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	if($method == 'POST'){
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
	}
	curl_setopt($ch, CURLOPT_TIMEOUT,300);
	$output = curl_exec($ch);
	$CurlError = @curl_error($ch);
	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	if($http_code != 200){
		return array('success' => false,'data' => !empty($CurlError) ? $CurlError : 'Unknow Error');
	}else{
		return array('success' => true,'data' => $output);
	}
}