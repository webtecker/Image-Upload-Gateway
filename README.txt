Upload Gateway

Requirements:
	PHP 5.2+
	PHP_CURL
	DropBox API Keys
	Amazon API Keys

Quick Setup Instructions:

	1.	Upload Files to Server that meet the above requirements.
	2.	Open the file located at app/config.php to set your Max File Size, Allowed File Extension,
	   	Disable/Enable Logs, Log Directory, Local Directory (Required), DropBox Keys and Amazon Keys.
	
	To Get Your DropBox API Keys:
	1.	Visit: https://www.dropbox.com/developers/apps. Create an account or Login and then click
		"Create App" and click DropBox API APP.
	2.	Then click "Files and Datastores" and "Yes My app only needs access to files it creates". 
	3.	Then add a name for your app. When you are done click the "Create App" button.
	4.	When you get to the next page. You shall see the fields "App key" and "App secret".
	5.  You should then add these keys to the app/config.php file.
	6.	The "App key" goes into the $config['directories']['dropbox']['key'] variable and 
		the "App secret" goes into the $config['directories']['dropbox']['secretKey'] variable
	Note: if you wish to change DropBox Apps you must delete any files in the tokens folder.

	To Get Your Amazon API Keys:
	1.	Visit: http://aws.amazon.com/s3/.  Click on "Create Free Account".
	2.  When you are done creating the account or logging in go to the upper right hand of the page and scroll
	    over "My Account/Console" and click "Security Credentials".
	3.	Click continue on the pop up then click the + button next to "Access Keys (Access Key ID and Secret Access Key".
	4.	Click "Create New Access Key" this will open a pop up and you can either download your access key or view it.
	5.	Add these keys to the app/config.php file the "Access Key ID" into the $config['directories']['amazon']['key'] variable
		and the Secret Access Key goes into the $config['directories']['amazon']['secretKey'] variable.
	6.	Now you must add a create a unique bucket name that goes into the $config['directories']['amazon']['bucket'] variable.


Upload Gateway Usage:
	Upload Files
	1. 	To upload a file you can Drag and Drop a file from a folder to the specified area or you can Browse for image
		by clicking on the "Or Browse Images..." button.
	2.	When done selecting files to upload select a Directory to upload the images to.
	3.	Click the "Upload Files" button when ready to upload files
	Note: Any directory you upload files to will automatically show up in the File Directory Section when the upload is complete.
	
	View Images
	1.	Click the File Directory Name button to view the uploaded images.
	
