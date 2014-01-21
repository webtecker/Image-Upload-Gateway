<?php
require_once('app/app.php');

if(!isset($_GET['directory'])){
	$directory = 'local';
} else {
	$directory = $_GET['directory'];
}
$directory = filter_var($directory, FILTER_SANITIZE_STRING);  	

$files = $upload->getFiles($directory);
?>
<div class="panel panel-default">
      <!-- Default panel contents -->
      <div class="panel-heading"><strong><?php echo ucfirst($directory); ?> Directory</strong></div>
    <table class="table table-striped">
        <thead>
            <tr><th>Image</th><th>Name</th></tr>
        </thead>
        <tbody>
            <?php 
			if(!empty($files)){
				foreach($files as $file){ ?>
                <tr>
                	<td><img src="<?php echo $file['image']; ?>" class="img-display" alt="<?php echo $file['fileName']; ?>" /></td>
                    <td><?php echo $file['fileName']; ?></td>
                </tr>
            <?php }//end loop
			} else {
			 ?>
             <tr colspan="2">
             	<td>No Images Exist</td>
             </tr>
            <?php } ?>
        </tbody>
    </table>
</div>