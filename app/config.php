<?php
/*
 * Config the File Center Project
 */

//Max Upload Size - in Megabytes
$config['max_upload_size'] = '5';

//Allowed File Extensions
$config['allowed_ext'] = 'jpg,jpeg,png,gif';

//Log Directory
$config['log_directory'] = './log';

//Log
$config['log'] = true;

/*****************************************Upload Directories*****************************************/
/*
 * Local Directory is Required Don't comment out.
 */
$config['directories']['local']['directory'] = './uploads';

/*
 * Comment out the variables below if you don't want to use DropBox
 * Get your DropBox Keys here - https://www.dropbox.com/developers/apps
 * And choose Dropbox API APP
 */
//$config['directories']['dropbox']['key'] = '';//Contains Your DropBox APP Key
//$config['directories']['dropbox']['secretKey'] = '';// Contiains Your DropBox Secret Key

/*
 * Comment out the variables below if you don't want to use Amazon
 * Sign up for your Amazon Keys here - http://aws.amazon.com/s3/
 */
//$config['directories']['amazon']['key'] = '';//Contains Your Amazon Access key
//$config['directories']['amazon']['secretKey'] = ''; //Contains Your Amazon Secret Key
//$config['directories']['amazon']['bucket'] = '';//Will automatically Create a Bucket if it doesn't already exist.

?>