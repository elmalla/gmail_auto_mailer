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
          <a  href="<?php echo site_url("admin").'/export/' ?>" class="btn btn-success">Show exports</a>
        </h2>
      </div>
      
 
        <!-- TRIAL -->
	<section>
            <div class="well">
                <div class="container text-center">

                    <p>Data exported to file : 
                    <?php 
                    
                     if (isset($count_urls)&&!empty($count_urls))
                        echo $count_urls;
                     
                     if (isset($count_emails)&&!empty($count_emails))
                        echo $count_emails ;       
                     ?></p>
                     </br></br>
                    <p>Exported file name: <?php echo $file?></p>
                       
                </div> <!-- end container -->
	    </div> <!-- end well -->
	</section>  
   
        <?php $i=0; if (isset($companies_url) )  :foreach($companies_url as $key => $value) {?>
             <li><?=$key?> : <?=$value?></li>  
        <?php if ($i >30){break;}
        $i++;}endif;?>
        
        <?php if (isset($errors) ):          ?>
          <div class="alert alert-error">
            <a class="close" data-dismiss="alert" href="#">×</a> <?php echo $errors;?>
          </div>
        <?php endif; ?>

          <?php echo '<div class="pagination">'.$this->pagination->create_links().'</div>'; ?>

      </div>
    </div>