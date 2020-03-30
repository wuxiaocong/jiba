<?php
$title = "级霸联盟";//网站标题
$index_go = "https://saikou.net";//十八禁目标地址
$data = file('cache.txt');
function send_post($url, $post_data) {
    $postdata = http_build_query($post_data);
    $options = array(
    'http' => array(
        'method' => 'POST',
        'header' => 'Content-type:application/x-www-form-urlencoded',
        'content' => $postdata,
        'timeout' => 15 * 60 // 超时时间（单位:s）
    )
  );
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return $result;
}
$jiba = array(
    'title' => $title,
    'domain' => $_SERVER['HTTP_HOST'],
	'version' => "1"
);
$url = 'https://da.jiba.show/';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_exec($ch);// $resp = curl_exec($ch);
$curl_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
if($data[0] < time()){
	if($curl_code == 200){
	$cachetime = time() + 1000;
	file_put_contents("cache.txt",$cachetime);
	$links = send_post('https://da.jiba.show/api.php', $jiba);
	file_put_contents("cache.txt", PHP_EOL.$links, FILE_APPEND);
	}else{$links = $data[1];}
}else{$links = $data[1];}
require_once ('base.php');
?>