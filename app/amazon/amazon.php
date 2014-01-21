<?php
/** 
 * A very simple standalone REST Amazon S3 class with only
 * 3 Operations getBucket, putBucket (addBucket), putObject (uploadFile).
 * 
 * 
 * @package     Upload Gateway 
 * @category    Library 
 * @author      Brett Bittke
 * @link        http://www.webtecker.com
 */ 

class amazon {
	
	/*
	 * AWS URI
	 *
	 * @var string
	 */
	public $awsUri = 's3.amazonaws.com';
	
	/*
	 * Access Key
	 *
	 * @var string
	 */
	public $accessKey;
	
	/*
	 * Secret Key
	 *
	 * @var string
	 */
	public $secretKey;
	
	/**
	 * CURL HTTP request headers
	 *
	 * @var array
	 */
	private $headers = array(
		'Host' => '', 'Date' => '', 'Content-MD5' => '', 'Content-Type' => ''
	);
	
	/**
	 * Curl request response
	 *
	 * @var object
	 */
	public $response;
	
	/**
	 * HTTP Method for Curl
	 *
	 * @var string
	 */
	public $method;
	
	/**
	 * Amazon specific request headers
	 *
	 * @var array
	 * @access private
	 */
	private $amzHeaders = array();
	
	/*
	 * Initializes the Class
	 *
	 * @param string Access Key
	 * @param string Secret Key
	 * @param string Bucket
	 */
	public function __construct($accessKey,$secretKey,$bucket=""){
		if(!empty($accessKey)){
			$this->accessKey = $accessKey;
		} else {
			die('Access Key is Requred');
		}
		if(!empty($secretKey)){
			$this->secretKey = $secretKey;
		} else {
			die('Secret Key is Required');
		}
		
		if(!empty($bucket)){
			$this->bucket = $bucket;
		}
	}//end __construct
	
	/*
	 * Get Bucket
	 *
	 * @param string Bucket
	 *
	 * @return array (fileName,image)
	 */
	public function getBucket($bucket=""){
		$data = array();
		if(!empty($bucket)){
			$this->bucket = $bucket;
		}
		if(empty($this->bucket)){
			die('Bucket is Required');
		}
		$this->headers['Host'] = $this->bucket.".".$this->awsUri;
		$this->headers['Date'] = gmdate('D, d M Y H:i:s T');
		$this->method = "GET";
		
		$response = $this->curl();
		
		if($response->code != 200){
			return false;
		}
		if (isset($response->body, $response->body->Contents)){
			foreach ($response->body->Contents as $content){
				$data[] = array('fileName' => (string)$content->Key,
								'image' => "https://".$this->awsUri."/".$this->bucket."/".$content->Key
								);
			}
		}//end if
		return $data;
	}//end getBucket
	
	/*
	 * Creates a Bucket in Amazon S3.
	 *
	 * @param string Bucket
	 *
	 * @return boolean
	 */
	public function addBucket($bucket){
		if(!empty($bucket)){
			$this->bucket = $bucket;
		}
		if(empty($this->bucket)){
			die('Bucket is Required');
		}
		$this->amzHeaders['x-amz-acl'] = 'private';
		$this->headers['Host'] = $this->bucket.".".$this->awsUri;
		$this->headers['Date'] = gmdate('D, d M Y H:i:s T');
		$this->method = "PUT";
		$response = $this->curl();
		
		if($response->code != 200){
			return true;	
		} else {
			return false;	
		}
	}//end addBucket
	
	/**
	* Upload File to Amazon S3
	*
	* @param array tmp_file
	* @param string $bucket Bucket name
	*
	* @return boolean
	*/
	public function uploadFile($file,$bucket=""){
		if(!empty($bucket)){
			$this->bucket = $bucket;
		}
		if(empty($this->bucket)){
			die('Bucket is Required');
		}
		$input = array('data' => fopen($file['tmp_name'], 'rw'),
					   'size' => filesize($file['tmp_name']),
					   'file'=>$file['name']);

		//headers
		$this->amzHeaders['x-amz-acl'] = 'public-read';
		$this->headers['Content-Type'] = $file['type'];
		$this->headers['Content-MD5'] = base64_encode(md5_file($file['tmp_name'], true));
		$this->headers['Host'] = $this->bucket.".".$this->awsUri;
		$this->headers['Date'] = gmdate('D, d M Y H:i:s T');
		$this->method = "PUT";
		$response = $this->curl($input);
		if($response->code != 200){
			return false;	
		} else {
			return true;	
		}
	}//end uploadFile
	
	
	/**
	* Generate the auth string: "AWS AccessKey:Signature"
	*
	* @param string $string String to sign
	* @return string
	*/
	public function getSignature($string){
		$hash = $this->getHash($string);
		$returnString = 'AWS '.$this->accessKey.':'.$hash;
		return $returnString;
	}


	/**
	* Creates a HMAC-SHA1 hash
	*
	* This uses the hash extension if loaded
	*
	* @internal Used by __getSignature()
	* @param string $string String to sign
	* @return string
	*/
	private function getHash($string)
	{
		return base64_encode(extension_loaded('hash') ?
		hash_hmac('sha1', $string, $this->secretKey, true) : pack('H*', sha1(
		(str_pad($this->secretKey, 64, chr(0x00)) ^ (str_repeat(chr(0x5c), 64))) .
		pack('H*', sha1((str_pad($this->secretKey, 64, chr(0x00)) ^
		(str_repeat(chr(0x36), 64))) . $string)))));
	}
	
	
	
	/**
	* Creates and Sends the CURL request to Amazon S3 Web Service.
	*
	* @param array file input for PUT method
	* @return array response
	*/
	public function curl($input=array()){
		//Create Resource and URL for CURL Request
		if(!empty($input)){
			$resource = "/".$this->bucket."/".$input['file'];
			$url = "http://".$this->bucket.".".$this->awsUri."/".$input['file'];
		} else {
			$resource = "/".$this->bucket."/";
			$url = "http://".$this->bucket.".".$this->awsUri;
		}
		//Create Response class
		$this->response = new STDClass;
		$this->response->error = false;
		$this->response->body = null;
		$this->response->headers = array();
		
		// Basic setup
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_USERAGENT, 'UploadGateway/php');	
		
		curl_setopt($curl, CURLOPT_URL, $url);
		
		//Create Headers
		$headers = array();
		$amz = array();
		foreach ($this->amzHeaders as $header => $value)
			if (strlen($value) > 0) $headers[] = $header.': '.$value;
		foreach ($this->headers as $header => $value)
			if (strlen($value) > 0) $headers[] = $header.': '.$value;
			
		// Collect AMZ headers for signature
		foreach ($this->amzHeaders as $header => $value)
			if (strlen($value) > 0) $amz[] = strtolower($header).':'.$value;
		if (sizeof($amz) > 0){
			$amz = "\n".implode("\n", $amz);
		} else {
			$amz = '';	
		}
		//Get Amazon Auth Header
		$headers[] = 'Authorization: ' . $this->getSignature(
					$this->method."\n".
					$this->headers['Content-MD5']."\n".
					$this->headers['Content-Type']."\n".
					$this->headers['Date'].$amz."\n".
					$resource
				);
					
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);		
		curl_setopt($curl, CURLOPT_WRITEFUNCTION, array(&$this, 'responseWriteCallback'));
		curl_setopt($curl, CURLOPT_HEADERFUNCTION, array(&$this, 'responseHeaderCallback'));
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		
		if($this->method == "PUT"){
			curl_setopt($curl, CURLOPT_PUT, true);
			if(!empty($input)){
				curl_setopt($curl, CURLOPT_INFILE, $input['data']);
				curl_setopt($curl, CURLOPT_INFILESIZE, $input['size']);
			}
		}
		
		if (curl_exec($curl)){
			$this->response->code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		} else {
			$this->response->error = array(
				'code' => curl_errno($curl),
				'message' => curl_error($curl),
				'resource' => $resource
			);
		}
		@curl_close($curl);
		
		if ($this->response->error === false && isset($this->response->headers['type']) &&
		$this->response->headers['type'] == 'application/xml' && isset($this->response->body)){
			
			$this->response->body = simplexml_load_string($this->response->body);
			
			if ($this->response->code != 200 && isset($this->response->body->Code, $this->response->body->Message)){
				
				$this->response->error = array('code' => (string)$this->response->body->Code,
											   'message' => (string)$this->response->body->Message
				);
				
				if (isset($this->response->body->Resource))
					$this->response->error['resource'] = (string)$this->response->body->Resource;
				unset($this->response->body);
			}
		}
		return $this->response;
	}//end curl
	
	/**
	* CURL write callback
	*
	* @param resource &$curl CURL resource
	* @param string &$data Data
	* @return integer
	*/
	private function responseWriteCallback(&$curl, &$data)
	{
		$this->response->body .= $data;
		return strlen($data);
	}

	/**
	* CURL header callback
	*
	* @param resource &$curl CURL resource
	* @param string &$data Data
	* @return integer
	*/
	private function responseHeaderCallback(&$curl, &$data)
	{
		if (($strlen = strlen($data)) <= 2) return $strlen;
		if (substr($data, 0, 4) == 'HTTP')
			$this->response->code = (int)substr($data, 9, 3);
		else
		{
			$data = trim($data);
			if (strpos($data, ': ') === false) return $strlen;
			list($header, $value) = explode(': ', $data, 2);
			if ($header == 'Last-Modified')
				$this->response->headers['time'] = strtotime($value);
			elseif ($header == 'Date')
				$this->response->headers['date'] = strtotime($value);
			elseif ($header == 'Content-Length')
				$this->response->headers['size'] = (int)$value;
			elseif ($header == 'Content-Type')
				$this->response->headers['type'] = $value;
			elseif ($header == 'ETag')
				$this->response->headers['hash'] = $value{0} == '"' ? substr($value, 1, -1) : $value;
			elseif (preg_match('/^x-amz-meta-.*$/', $header))
				$this->response->headers[$header] = $value;
		}
		return $strlen;
	}
	
}//end class

?>