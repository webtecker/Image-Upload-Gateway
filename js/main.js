$(document).ready( function() {
	// Get all of the data URIs and put them in an array
	var fileList = [];
	//Load File List
	$('#upload-content').load('files.php');
	//Load File list based on Button click
	$('.btn-directory').click(function(e){
		var btn = $(this);
		$('#file-loading').show();
		$( '.btn-directory' ).each(function( index ) {
		  	$(this).removeClass('active');
		});
		btn.addClass('active');
		$('#upload-content').load(btn.attr('href'), function () { //calback function
        	 $('#file-loading').hide();
    	});
		e.preventDefault();
	});
	
	//Add Files to File List Input.
	$('.btn-file :file').bind('change', function(){
		var input = $(this),
		list = $("#file-list"),
		numFiles = input.get(0).files ? input.get(0).files.length : 1,//get number of files
		numFiles = numFiles + fileList.length,
		fileName = input.val().replace(/\\/g, '/').replace(/.*\//, ''),//filter folders out of file name
		content = numFiles > 1 ? numFiles + ' files selected' : fileName;
		list.val(content);
		if(input.get(0).files.length > 0){ // Check to see if Files added via Browse If so add them to fileList
			for (var i = 0, f; f = input.get(0).files[i]; i++) {
				if (!f.type.match('image.*')) {
					alert('Please Only Submit Images');
					input.val('');
					$("#file-list").val('');
					return false;	
				}
				fileList.push(f);
			}
		}//end browsedFile length check
		
	});
	
	
	//Allow Drag and Drop Files and Add files to fileList
	$("#drop-files").bind('drop', function(e) {
		//Get Files
		var files = e.dataTransfer.files;
		// Loop through files
    	$.each(files, function(index, file) {
			//check to make sure its an image
			if (!files[index].type.match('image.*')) {
				alert('Please Only Submit Images');
				$("#file-list").val('');
				return false;	
			}
			fileList.push(file);
			var content = fileList.length > 1 ? fileList.length + ' files selected' : file.name;
			$("#file-list").val(content);
		});//end loop
	});//end bind drop
	
	//css styling
	$('#drop-files').bind('dragenter', function(e) {
		$(this).css({'box-shadow' : 'inset 0px 0px 20px rgba(0, 0, 0, 0.1)', 'border' : '4px dashed #bb2b2b'});
		e.preventDefault();
	});
	//css styling
	$('#drop-files').bind('drop', function(e) {
		$(this).css({'box-shadow' : 'none', 'border' : '4px dashed rgba(0,0,0,0.2)'});
		e.preventDefault();
	});
	
	//Upload Files and Add the Browse File to fileList
	$('#btn-upload').click(function(e){
		var directory = $('#directory').val(),
			error = 0,
			missing = "";
		//Remove Error Classes just in case they were there prior
		$('#directory').parent().removeClass('has-error');
		$('#file-list').parent().parent().removeClass('has-error');
		//Check for Errors
		if(directory == ""){
			error = 1;
			missing += "Directory";
			 $('#directory').parent().addClass('has-error');
			 $('#directory').parent().append('<p class="help-block">Please Select a Directory</p>');
		}
		if(fileList.length == 0){
			error = 1;
			 if(missing != ""){
				 missing += " and "
			 }
			 missing += "Files";
			 $('#file-list').parent().parent().addClass('has-error');
			 $('#file-list').parent().parent().append('<p class="help-block">Please Select Files To Upload</p>');
		}
		if(error == 1){
			alert('Missing Fields: '+missing);
		} else {
			//Reset all css and HTML to original.
			$('#progress-bar').css({'width' : '5%'});
			$('#success').removeClass('alert-success').addClass('alert-info')
						 .html('<strong>Uploading:</strong> <span id="file-name"></span>');
			//Show Progress Bar
			$('#progress-bar').parent().addClass('active');
			$('#upload-progress').show();
			//Set Total Percent
			var totalPercent = 100 / fileList.length,
				x = 0;
			//Now Loop through fileList and Save and Upload file
			$.each(fileList, function(index, file) {
				var formData = new FormData();
				formData.append('file',file);
				formData.append('directory',directory);
				$.ajax({ url: "upload.php",  
    					 type: "POST",  
						 data: formData,
						 dataType: 'json',
						 contentType: false,
    					 processData: false,
						 success: function (data) { 
							++x;
							$('#progress-bar').css({'width' : totalPercent*(x)+'%'});
							$("#file-name").html(file.name);
							if(data.success){
								if(totalPercent*(x) == 100) {//Upload is complete
									//Reset Files so Another Upload can happen
									$("#file-list").val('');
									$("#files").val('');
									fileList = [];
									//Remove active Progress Bar
									$('#progress-bar').parent().removeClass('active');
									//Load Files
									$('#file-loading').show();
									$('#upload-content').load('files.php?directory='+directory, function () { //calback function
										 $('#file-loading').hide();
										 $( '.btn-directory' ).each(function( index ) {
											 if($(this).text().toLowerCase() != directory ){
												$(this).removeClass('active');
											 } else {
												 $(this).addClass('active');
											 }
										});
									});
									//Show success Loop
									$('#success').removeClass('alert-info').addClass('alert-success')
												 .html('<strong>Files Have Been Uploaded!</strong>');
								}//end 100% complete
							} else {
								//Display Error
								$('#error').show().append("<p>"+data.message+"</p>");
							}
						 }//end Success
				});//end post
			});//end loop	
		}//end missing check
		e.preventDefault();
	});
	
	//Allow Progress to be closed
	$('#progress-close').click(function(){
		$(this).parent().hide();
	});
});		
