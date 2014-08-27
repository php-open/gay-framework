<?php
/**
* @author Connor caokang@foxmail.com
* @abstract 3des
*/
 
class DES {
	
	private $key = 'DES_KEY';
	//只有CBC模式下需要iv，其他模式下iv会被忽略 
	//private $iv = 'B500290096000A007600E3005700C4003800A40018008500';
	private $iv = '12345678';
	
	function DES($key=''){
		$this->key = $key;
	}
	/**
	 * 加密
	 */
	public  function encrypt($value) {
		//先确定加密模式，此处以ECB为例
		$td = mcrypt_module_open ( MCRYPT_3DES,'', MCRYPT_MODE_ECB,'');
		//$iv = pack ( 'H16', $this->iv );
		$value = $this->PaddingPKCS7 ( $value ); //填充
		//$key = pack ( 'H48', $this->key );
		mcrypt_generic_init ( $td, $this->key, $this->iv);
		$ret = base64_encode ( mcrypt_generic ( $td, $value ) );
		mcrypt_generic_deinit ( $td );
		mcrypt_module_close ( $td );
	 
		return $ret;
	}
	 
	/**
	* 解密
	*/
	public  function decrypt($value) {
		$td = mcrypt_module_open ( MCRYPT_3DES, '', MCRYPT_MODE_ECB, '' );
		//$iv = pack ( 'H16', $this->iv );
		//$key = pack ( 'H48', $this->key );
		mcrypt_generic_init ( $td, $this->key,$this->iv );
		$ret = trim ( mdecrypt_generic ( $td, base64_decode ( $value ) ) );
		$ret = $this->UnPaddingPKCS7 ( $ret );
		mcrypt_generic_deinit ( $td );
		mcrypt_module_close ( $td );
		return $ret;
	}
	
	private  function PaddingPKCS7($data) {
		$padlen =  8 - strlen( $data ) % 8 ;
		for($i = 0; $i < $padlen; $i ++)$data .= chr( $padlen );
		return $data;
	}
	 
	private  function UnPaddingPKCS7($data) {
		$padlen = ord (substr($data, (strlen( $data )-1), 1 ) );
		if ($padlen > 8 )return $data;
			for($i = -1*($padlen-strlen($data)); $i < strlen ( $data ); $i ++) {
			if (ord ( substr ( $data, $i, 1 ) ) != $padlen)return false;
		}
		return substr ( $data, 0, -1*($padlen-strlen ( $data ) ) );
	}
}


/*
* AES 加密算法 
*
$aes = new aes();
$aes->setKey('key');
 
// 加密
$aes->encode('string');
// 解密
$aes->decode($string);
*/ 
class aes {
 
    // CRYPTO_CIPHER_BLOCK_SIZE 32
 
private $_secret_key = 'default_secret_key';
 
public function setKey($key) {
	$this->_secret_key = $key;
}
 
public function encode($data) {
	$td = mcrypt_module_open(MCRYPT_RIJNDAEL_256,'',MCRYPT_MODE_CBC,'');
	$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td),MCRYPT_RAND);
	mcrypt_generic_init($td,$this->_secret_key,$iv);
	$encrypted = mcrypt_generic($td,$data);
	mcrypt_generic_deinit($td);
	 
	return $iv . $encrypted;
}
 
public function decode($data) {
	$td = mcrypt_module_open(MCRYPT_RIJNDAEL_256,'',MCRYPT_MODE_CBC,'');
	$iv = mb_substr($data,0,32,'latin1');
	mcrypt_generic_init($td,$this->_secret_key,$iv);
	$data = mb_substr($data,32,mb_strlen($data,'latin1'),'latin1');
	$data = mdecrypt_generic($td,$data);
	mcrypt_generic_deinit($td);
	mcrypt_module_close($td);
	 
	return trim($data);
}
}

?>
