<?php
/**
* Mode：惠州市自来水总公司水费模拟查询系统
* User: pkkgu 910111100@qq.com
* Date: 2014年5月28日
* 请在GB2312编码下使用
* 代码因个人爱好编写，请不要用于商业用途
*/
error_reporting(0);

class Water{
	private $card; //电脑卡号
	private $username; //姓名
	private $time=3; //查询月份
    /**
     * 构造函数
     * @param int $card 电脑卡号
     * @param string $username 姓名
     * @param int $time 查询月份
     */
    public function __construct()
    {
    }
	/**
	 * @desc input string of html <table......</table>
	 * @param string $table
	 * @return array
	 */
    public function tableToArray($table)
	{
		$result = array();
		//去掉换行、空白字符
		$table = preg_replace("/([\r\n\s]+)/", "", $table);
	
		// 处理开始标签
		$pattern = array("'<table[^>]*?>'si", "'<tr[^>]*?>'si", "'<td[^>]*?>'si");
		$replacement = array('', '', '');
		$table = preg_replace($pattern, $replacement, $table);
	
		// 处理结束标签
		$search = array('</tr>', '</td>');
		$replacement = array('{tr}', '{td}');
		$table = str_ireplace($search, $replacement, $table);
	
		//去掉 HTML 标记
		$table = strip_tags($table);
	
		$table = explode('{tr}', $table);
		array_pop($table);
		foreach ($table as $tr) {
			$td = explode('{td}', $tr);
			array_pop($td);
			$result[] = $td;
		}
		return $result;
	}
	/**
	 * POST 请求
	 * @param string $url
	 * @param array $param
	 * @return string content
	 */
	public function http_post($url,$param){
		header ( "Content-Type: text/html; charset=GB2312" );
		$oCurl = curl_init();
		if(stripos($url,"https://")!==FALSE){
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
		}
		if (is_string($param)) {
			$strPOST = $param;
		} else {
			$aPOST = array();
			foreach($param as $key=>$val){
				$aPOST[] = $key."=".($val);
			}
			$strPOST =  join("&", $aPOST);
		}
		curl_setopt($oCurl, CURLOPT_URL, $url);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($oCurl, CURLOPT_POST,true);
		curl_setopt($oCurl, CURLOPT_POSTFIELDS,$strPOST);
		$sContent = curl_exec($oCurl);
		$aStatus = curl_getinfo($oCurl);
		curl_close($oCurl);
		if(intval($aStatus["http_code"])==200){
			return $sContent;
		}else{
			return false;
		}
	}
}

// 查询过程开始
$arr=array();
$arr['sfmainid']=htmlspecialchars($_GET['card']);
$arr['sfname']=htmlspecialchars($_GET['username']);
$arr['sfqrynum']=$_GET['time']?(int)$_GET['time']:3;
//$arr['sfname']=iconv("UTF-8","GB2312//IGNORE",$arr['sfname']);
if($arr['sfmainid']||$arr['sfname'])
{
	/* 生成上传实例对象并完成上传 */
	$Water = new Water();
	// 发起请求
	$ret   = $Water->http_post('http://www.hzwater.gd.cn/feequery.php',$arr);
	// 格式化表格数据
	$data  = $Water->tableToArray($ret);
	// 打印结果
	echo "<pre>";
	print_r($data);
}
else
{ echo '<script>alert("请输入电脑编号或用户名称");</script>'; }
?>


<!-- 查询表单 -->
<form action="">
电脑编号：<input type="text" name="card" value="" />
用户名称：<input type="text" name="username" value="" />
查询最近：<input type="text" name="time" value="3" />
<input type="submit" name="submit" value="提交" />
</form>