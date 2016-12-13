    <div class="container top">
      
      <ul class="breadcrumb">
        <li>
          <a href="<?php echo site_url("admin"); ?>">
            <?php echo ucfirst($this->uri->segment(1));?>
          </a> 
          <span class="divider">/</span>
        </li>
        <li>
          <a href="<?php echo site_url("admin").'/'.$this->uri->segment(2); ?>">
            <?php echo ucfirst($this->uri->segment(2));?>
          </a> 
          <span class="divider">/</span>
        </li>
        <li class="active">
          <a href="#">Update</a>
        </li>
      </ul>
      
      <div class="page-header">
        <h2>
          Updating <?php echo ucfirst($this->uri->segment(2));?>
        </h2>
      </div>

 
      <?php
      //flash messages
      if($this->session->flashdata('flash_message')){
        if($this->session->flashdata('flash_message') == 'updated')
        {
          echo '<div class="alert alert-success">';
            echo '<a class="close" data-dismiss="alert">×</a>';
            echo '<strong>Well done!</strong> owner updated with success.';
          echo '</div>';       
        }else{
          echo '<div class="alert alert-error">';
            echo '<a class="close" data-dismiss="alert">×</a>';
            echo '<strong>Oh snap!</strong> change a few things up and try submitting again.';
          echo '</div>';          
        }
      }
      ?>
      
      <?php
      //form data
      $attributes = array('class' => 'form-horizontal', 'id' => '');
      
      //form validation
      echo validation_errors();

      echo form_open('admin/Owner/update/'.$this->uri->segment(4).'', $attributes);
      ?>
        <fieldset>
          
          <div class="control-group">
            <label for="inputError" class="control-label">Owner Name</label>
            <div class="controls">
              <input type="text" id="" name="owner_name" value="<?php echo $owner['owner']; ?>" >
              <!--<span class="help-inline">Woohoo!</span>-->
            </div>
          </div>
          
                    
          <div class="control-group">
            <label for="inputError" class="control-label">Owner Id</label>
            <div class="controls">
              <input type="text" id="" name="owner_id" value="<?php echo $owner['id']; ?>">
              <!--<span class="help-inline">Cost Price</span>-->
            </div>
          </div>        
          
          
          <div class="form-actions">
            <button class="btn btn-primary" type="submit">Save changes</button>
            <button class="btn" type="reset">Cancel</button>
          </div>
        </fieldset>

      <?php echo form_close(); ?>
     
        <table class="table table-striped table-bordered table-condensed">
            <thead>
             
            </thead>
            <tbody>
              <?php
              $id=0;
              
                      //$id++;
                    echo '<tr>';
                    echo '<td><a href="'.site_url("admin").'/Owner/update/'.'1'.'" class="btn btn-info">First</a></td>';
                    echo '<td><a href="'.site_url("admin").'/Owner/update/'.$count_owner.'" class="btn btn-info">Previous</a></td>';
                    echo '<td><a href="'.site_url("admin").'/Owner/update/'.$count_owner.'" class="btn btn-info">Next</a></td>';
                    echo '<td><a href="'.site_url("admin").'/Owner/update/'.$count_owner.'" class="btn btn-info">Last</a></td>';
                   
                    
                    echo '</tr>';
             
              ?>      
            </tbody>
          </table>
    </div>
   
     