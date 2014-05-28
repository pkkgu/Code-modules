<?php
/**
* Mode������������ˮ�ܹ�˾ˮ��ģ���ѯϵͳ
* User: pkkgu 910111100@qq.com
* Date: 2014��5��28��
* ����GB2312������ʹ��
* ��������˰��ñ�д���벻Ҫ������ҵ��;
*/
error_reporting(0);

class Water{
	private $card; //���Կ���
	private $username; //����
	private $time=3; //��ѯ�·�
    /**
     * ���캯��
     * @param int $card ���Կ���
     * @param string $username ����
     * @param int $time ��ѯ�·�
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
		//ȥ�����С��հ��ַ�
		$table = preg_replace("/([\r\n\s]+)/", "", $table);
	
		// ����ʼ��ǩ
		$pattern = array("'<table[^>]*?>'si", "'<tr[^>]*?>'si", "'<td[^>]*?>'si");
		$replacement = array('', '', '');
		$table = preg_replace($pattern, $replacement, $table);
	
		// ���������ǩ
		$search = array('</tr>', '</td>');
		$replacement = array('{tr}', '{td}');
		$table = str_ireplace($search, $replacement, $table);
	
		//ȥ�� HTML ���
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
	 * POST ����
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

// ��ѯ���̿�ʼ
$arr=array();
$arr['sfmainid']=htmlspecialchars($_GET['card']);
$arr['sfname']=htmlspecialchars($_GET['username']);
$arr['sfqrynum']=$_GET['time']?(int)$_GET['time']:3;
//$arr['sfname']=iconv("UTF-8","GB2312//IGNORE",$arr['sfname']);
if($arr['sfmainid']||$arr['sfname'])
{
	/* �����ϴ�ʵ����������ϴ� */
	$Water = new Water();
	// ��������
	$ret   = $Water->http_post('http://www.hzwater.gd.cn/feequery.php',$arr);
	// ��ʽ���������
	$data  = $Water->tableToArray($ret);
	// ��ӡ���
	echo "<pre>";
	print_r($data);
}
else
{ echo '<script>alert("��������Ա�Ż��û�����");</script>'; }
?>


<!-- ��ѯ�� -->
<form action="">
���Ա�ţ�<input type="text" name="card" value="" />
�û����ƣ�<input type="text" name="username" value="" />
��ѯ�����<input type="text" name="time" value="3" />
<input type="submit" name="submit" value="�ύ" />
</form>