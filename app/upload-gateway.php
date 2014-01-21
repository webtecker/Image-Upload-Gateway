<?php

/** 
 * Upload Gateway is a Container for the Amazon Class and DropPHP class.
 * 
 * 
 * @package     Upload Gateway 
 * @author      Brett Bittke
 * @link        http://www.webtecker.com
 */ 
class uploadGateway {
	
	/** 
     * Config values created in Config.php file.
     * 
     * @var array
     */ 
	public $config;
	
	/** 
     * Temporary File Name for upload
     * 
     * @var string 
     */ 
	public $temp_file_name; 
	
	/** 
     * Uploaded File Name
     * 
     * @var string 
     */ 
    public $file_name; 
	
	/** 
     * Uploaded File Name
     * 
     * @var class
     */ 
	public $dropbox;
	
	/** 
     * Uploaded File Name
     * 
     * @var class 
     */ 
	public $amazon;
	
	/** 
     *Initialize Class and additional Classes for File Upload
	 *
	 *@param array configuration array
	 *
	 */
	public function __construct($config) {
		$this->config = $config;
		
		//Initialize Dropbox
		if(isset($config['directories']['dropbox'])){
			if($config['directories']['dropbox']['key'] == "" && $config['directories']['dropbox']['secretKey'] == ""){
				$this->logResults(false,"Dropbox Key info is Empty in the Config File.");
			} else {
				require_once('dropbox/DropboxClient.php');
				$this->dropbox = new DropboxClient(array(
								'app_key' => $config['directories']['dropbox']['key'],
								'app_secret' => $config['directories']['dropbox']['secretKey'],
								'app_full_access' => false,
								),'en');
			}
		}
		//Initialize Amazon
		if(isset($config['directories']['amazon'])){
			if($config['directories']['amazon']['key'] == "" && $config['directories']['amazon']['secretKey'] == ""){
				$this->logResults(false,"Amazon Key info is Empty in the Config File.");
			}
			require_once('amazon/amazon.php');
			$this->amazon = new amazon($config['directories']['amazon']['key'],
											 $config['directories']['amazon']['secretKey']);
			//If Bucket Doesn't Exist Add it
			if($this->amazon->getBucket($config['directories']['amazon']['bucket']) === false){
				$this->amazon->addBucket($config['directories']['amazon']['bucket']);
			} 
		}
	}
	
	
	/*
	 * Main Function to Upload the File to the Specified Service or Local
	 *
	 *@param array from $_FILE
	 *@param string 
	 *
	 *@return json
	 */
	public function upload($file,$directory){
		$data = array('success'=>false,'message'=>'');
		$error = false;
		//Allowed Extension Check
		$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
		$allowed_ext = explode(",",$this->config['allowed_ext']);
		if(!in_array($ext,$allowed_ext)){
			$error = true;
			$data = array('success'=>false,'message'=>"File File (".$file['name'].") Extension is no allowed.");	
		}
		//Max File Size Check
		if($file['size']> $this->config['max_upload_size'] * (1024 * 1024)){
			$error = true;
			$data = array('success'=>false,'message'=>"File (".$file['name'].") is too Large");	
		}
		if($error === false){
			switch ($directory) {
				case "local":
					if(move_uploaded_file($file['tmp_name'],
					   $this->config['directories']['local']['directory']."/". $file['name'])){
						$data = array('success'=>true,
									  'message'=>"File (".$file['name'].") has been Uploaded!");
					} else {
						$data = array('success'=>false,
									  'message'=>"An Error has occured when Uploading File: ".$file['name']."");
					}
					break;
				case "dropbox":
					$results = $this->dropbox->UploadFile($file['tmp_name'],$file['name']);
					if(!empty($results)){
						$data = array('success'=>true,'message'=>"File (".$file['name'].") has been Uploaded!");
					} else {
						$data = array('success'=>false,
									  'message'=>"An Error has occured when Uploading File to Dropbox: ".$file['name']."");
					}
					break;
				case "amazon":
					if( $this->amazon->uploadFile($file, $this->config['directories']['amazon']['bucket']) ){
						 $data = array('success'=>true,'message'=>"File (".$file['name'].") has been Uploaded!");
					 } else {
						 $data = array('success'=>false,
						 			   'message'=>"An Error has occured when Uploading File to Amazon: ".$file['name']."");
					 }
					break;
			}//end switch case
		}//end error check
		$this->logResults($data);
		return json_encode($data);
	}//end upload
	
	/*
	 * Gets Files based on the directory and returns as an array
	 *
	 *@param string
	 *@return array [fileName, image]
	 */
	public function getFiles($directory){
		$data = array();
		switch ($directory) {
			case "local":
				if ($handle = opendir($this->config['directories']['local']['directory'])) {
					while (false !== ($entry = readdir($handle))) {
						if ($entry != "." && $entry != "..") {
							if(is_dir("./".$this->config['directories']['local']['directory']."/".$entry)) { continue; }
							$data[] = array('fileName' => $entry,
											'image' => "".$this->config['directories']['local']['directory']."/".$entry);
						}
					}
					closedir($handle);
			   }
				break;
			case "dropbox":
				$files = $this->dropbox->getFiles();
				foreach($files as $file=>$metaData){
					$thumbnail = $this->dropbox->GetThumbnail($file,'m','jpeg',false);
					$data[] = array('fileName' => $file,
									'image' => "data:image/jpeg;base64,".base64_encode($thumbnail)."");
				}
				break;
			case "amazon":
				$data = $this->amazon->getBucket($this->config['directories']['amazon']['bucket']);
				break;
		}//end switch case
		return $data;
	}//end getFiles function
	
	
	/** 
     * Logs the results 
     * 
     * @param array
     * @return void 
     */ 
    private function logResults($log) 
    { 

        if (!$this->config['log']) return; 
        $text = '[' . date('m/d/Y g:i A').'] - ';//Time Stamp
        $text .= ($log['success']) ? "SUCCESS! " : 'FAIL: '; // Success or failure being logged? 
		$text .=  $log['message'] . "\n";
        // Write to log 
		$fp = fopen($this->config['log_directory']."/gateway.log.txt",'a+'); 
		fwrite($fp, $text); 
        fclose($fp); 
    } //end logResults
}

?>