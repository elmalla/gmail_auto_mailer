    <div class="container top">

      <ul class="breadcrumb">
        <li>
          <a href="<?php echo site_url("admin"); ?>">
            <?php echo ucfirst($this->uri->segment(1));?>
          </a> 
          <span class="divider">/</span>
        </li>
        <li class="active">
          <?php echo ucfirst($this->uri->segment(2));?>
        </li>
      </ul>

      <div class="page-header users-header">
        <h2>
          <?php echo ucfirst($this->uri->segment(2));?> 
          
        </h2>
      </div>
      
 
        <!-- TRIAL -->
	<section>
            <div class="well">
                <div class="container text-center">

                    <p>Select File To Upload</p>

                    <form action="do_uploads" class="form-inline" role="form" method="POST" enctype="multipart/form-data">

                            <div class="form-group">
                                    <label for="uploading-files" class="sr-only">File Name</label>
                                    <input type="file" class="form-control" id="uploading-files" name="userfile"  multiple="multiple">
                                    <br /><br />
                                    <button type="submit" name="submit" value="Upload" class="btn btn-primary">Upload</button>
                            </div> <!-- end form-group -->

                    </form>
                </div> <!-- end container -->
	    </div> <!-- end well -->
	</section>  
   
        <?php if (isset($uploaded_data) ): foreach($uploaded_data as $key => $value) {?>
             <li><?=$key?> : <?=$value?></li>  
        <?php }endif;?>
        
        <?php if (isset($errors) ):          ?>
          <div class="alert alert-error">
            <a class="close" data-dismiss="alert" href="#">×</a> <?php echo $errors;?>
          </div>
        <?php endif; ?>

          <?php echo '<div class="pagination">'.$this->pagination->create_links().'</div>'; ?>

      </div>
    </div>