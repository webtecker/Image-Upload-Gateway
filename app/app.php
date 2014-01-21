<?php
/*
 * app.php is a container for the config and upload-gateway
 *
 * @package     Upload Gateway 
 * @author      Brett Bittke
 * @link        http://www.webtecker.com
 */ 

//Include Config and Upload Gateway
require_once('app/config.php');
require_once('app/upload-gateway.php');

//Initialize UploadGateway
$upload = new uploadGateway($config);

/*
 * Below is the OAuth Validation For DropBox API.
 *
 */
if(isset($config['directories']['dropbox'])){
	// first try to load existing access token
	$access_token = $upload->dropbox->load_token("access");
	if(!empty($access_token)) {
			$upload->dropbox->SetAccessToken($access_token);
	} elseif(!empty($_GET['auth_callback'])) { // are we coming from dropbox's auth page?
			// then load our previosly created request token
			$request_token = $upload->dropbox->load_token($_GET['oauth_token']);
			if(empty($request_token)) die('Request token not found!');
			
			// get & store access token, the request token is not needed anymore
			$access_token = $upload->dropbox->GetAccessToken($request_token);        
			$upload->dropbox->store_token($access_token, "access");
			$upload->dropbox->delete_token($_GET['oauth_token']);
	}
	// checks if access token is required
	if(!$upload->dropbox->IsAuthorized()){
			// redirect user to dropbox auth page
			$return_url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']."?auth_callback=1";
			$auth_url = $upload->dropbox->BuildAuthorizeUrl($return_url);
			$request_token = $upload->dropbox->GetRequestToken();
			$upload->dropbox->store_token($request_token, $request_token['t']);
			die("<h1>DropBox Authentication Required. <a href='$auth_url' class='btn btn-primary'>Click Here For Authentication/a></h1>");
	}
}//end Dropbox OAuth Check.

?>