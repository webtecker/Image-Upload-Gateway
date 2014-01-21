<!DOCTYPE html>
<html class="no-js">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Image Upload Gateway</title>
        <meta name="description" content="">

        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="css/main.css">
        <script src="js/vendor/modernizr-2.7.0.min.js"></script>
    </head>
    <body>
        <!--[if lt IE 8]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
        <?php
			//Alows you to Access $config array and $upload class.  Also Gets DropBox Registered
			require_once('app/app.php');
		?>
        <div id="wrap">
        	<div class="container">
                <header class="page-header">
                    <h1>Image Upload Gateway</h1>
                </header>
            
                <div class="row">
                    <div class="col-lg-12">
                    <!-- Upload Form -->
                        <section id="form-upload">
                        	<div class="well" id="upload-progress" style="display:none;">
                            <button id="progress-close" type="button" class="close" aria-hidden="true">&times;</button>
                            	<h3>Upload Progress</h3>
                                <div class="progress progress-striped active">
                                  <div class="progress-bar" id="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                    <span class="sr-only">0% Complete</span>
                                  </div>
                                </div>
                                <div class="alert alert-info" id="success">
                                    <strong>Uploading:</strong> <span id="file-name"></span>
                                </div>
                                <div class="alert alert-danger" id="error" style="display:none;">
                                    
                                </div>
                            </div>
                        
                            <form id="file-upload" >
                                <div class="form-group">
                                    <label for="file" class="control-label">Upload your Images <span style="color:red;">*</span></label>
                                    <!-- ondragover for firefox -->
                                    <div id="drop-files" ondragover="return false">Drop Images</div>
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <span class="btn btn-primary btn-file">
                                                Or Browse Images… <input type="file" multiple="" name="files" id="files">
                                            </span>
                                        </span>
                                        <input type="text" id="file-list" class="form-control" readonly="">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="directory" class="control-label">Select a Directory to Upload your Images <span style="color:red;">*</span></label>
                                    <select id="directory" name="directory" class="form-control">
                                        <option value="">Select a Directory</option>
                                        <?php
                                            foreach($config['directories'] as $directory=>$keys){
                                                echo "<option value='$directory'>".ucfirst($directory)."</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                	<input type="submit" id="btn-upload" class="btn btn-primary" name="upload" value="Upload">
                                    <p><span style="color:red;">*</span> denotes required fields</p>
                                </div>
                            </form>
                        </section>
                        <!-- Upload Director -->
                        <section id="directory">
                        	<header class="page-header">
                            	<h2>File Directory <small>Click a directory below to view files.</small></h2>
                            </header>
                        	<div class="btn-group btn-group-justified">
								<?php foreach($config['directories'] as $directory=>$keys){ ?>
                                    <a href="files.php?directory=<?php echo $directory; ?>" class="btn btn-info btn-directory <?php if($directory == 'local'){ echo "active";} ?>"><?php echo ucfirst($directory); ?></a>
                                <?php } ?>
                            </div>
                            <div id="file-loading" class="loading" style="display:none;"></div>
                            <div id="upload-content"></div><!-- Loaded via AJAX based on Drop Down -->
                        </section>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer -->
        <footer id="footer">
            <div class="container">
                <p class="text-muted credit">Coded by Brett Bittke</p>
            </div>
        </footer>
        
        

        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.10.2.min.js"><\/script>')</script>
        <script src="js/plugins.js"></script>
        <script src="js/main.js"></script>
    </body>
</html>
