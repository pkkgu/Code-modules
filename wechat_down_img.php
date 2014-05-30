<?php
/**
 * Mode：微信图片高速本地化
 * User: pkkgu 910111100@qq.com
 * Date: 2014年5月30日
 * 代码因个人爱好编写，请不要用于商业用途
 * @param $url string 微信专用图片地址
 * @param $save_dir string 图片存放目录
 * @param $filename string 保存文名(为空用时间戳+)
 * @param return array
 */
function getFile($url,$save_dir='',$filename=''){
	$url=trim($url);
	$url=str_replace(" ","%20",$url);
	if(empty($url)){
		return false;
	}
	if(trim($save_dir)=='')
	{
		$save_dir='./';
	}
	elseif(0!==strrpos($save_dir,'/'))
	{
		$save_dir.='/';
	}
	if(empty($filename))
	{
		$filename=time().rand(100,999).".jpg";
	}
	//创建保存目录
	if(!file_exists($save_dir)&&!mkdir($save_dir,0777,true)){
		return false;
	}
	//获取远程文
	$content=http_get($url);
	//文件大小
	$size=strlen($content);
	//写文件
	$filepath=$save_dir.$filename;
	WriteFiletext_n($filepath,$content);
	unset($content,$url);
	return array('file_name'=>$filename,'save_path'=>$save_dir.$filename,'size'=>$size);
}
/**
 * GET 请求
 * @param string $url
 */
function http_get($url){
	$oCurl = curl_init();
	if(stripos($url,"https://")!==FALSE){
		curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
	}
	curl_setopt($oCurl, CURLOPT_URL, $url);
	curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
	$sContent = curl_exec($oCurl);
	$aStatus = curl_getinfo($oCurl);
	curl_close($oCurl);
	if(intval($aStatus["http_code"])==200){
		return $sContent;
	}else{
		return false;
	}
}
/* 写文件 */
function WriteFiletext_n($filepath,$string){
	$fp=@fopen($filepath,"w");
	@fputs($fp,$string);
	@fclose($fp);
	@chmod($filepath,0777);
}

$url="http://mmbiz.qpic.cn/mmbiz/s2GQnHmKjibZFL0anH9FgVRrxarIstDa0H9Cmdhib8lfyxy5BJevcVMWjWE3t1vJXFqib4ekH0QsjQtRA80v7mlUQ/0";
$img=getFile($url,'','');
echo "<pre>";
print_r($img);
?>