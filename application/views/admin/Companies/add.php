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
          <a href="#">New</a>
        </li>
      </ul>
      
      <div class="page-header">
        <h2>
          Adding <?php echo ucfirst($this->uri->segment(2));?>
        </h2>
      </div>
 
      <?php
      //flash messages
      if($this->session->flashdata('flash_message')){
        if($this->session->flashdata('flash_message') == 'sucess')
        {
          echo '<div class="alert alert-success">';
            echo '<a class="close" data-dismiss="alert">×</a>';
            echo '<strong>Well done!</strong> new owner created with success.';
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
     
    if (is_array($Categories)&& (count($Categories) >1 ))
    {
      $options_category = array('' => "Select");    
      foreach ($Categories as $array) {
          foreach ($array as $key => $value) {
            $option_category[$key] = $key;
          }
          break;
      } 

    }else
        $option_category =array("$Categories[1]"=>$Categories[1]);
      
      //form data
      $attributes = array('class' => 'form-horizontal', 'id' => '');

      //form validation
      echo validation_errors();
      
      echo form_open('admin/Comapnies/add', $attributes);
      ?>
        <fieldset>
          <div class="control-group">
            <label for="inputError" class="control-label">Company Name</label>
            <div class="controls">
              <input type="text" id="" name="company_name" value="<?php echo set_value('company_name'); ?>" >
              <!--<span class="help-inline">Woohoo!</span>-->
            </div>
          </div>
          
                    
          <div class="control-group">
            <label for="inputError" class="control-label">Country</label>
            <div class="controls">
              <input type="text" id="" name="country" value="<?php echo set_value('country'); ?>">
              <!--<span class="help-inline">Country</span>-->
            </div>
          </div>
            
          <div class="control-group">
            <label for="inputError" class="control-label">URL</label>
            <div class="controls">
              <input type="text" id="" name="url" value="<?php echo set_value('url'); ?>">
              <!--<span class="help-inline">Cost Price</span>-->
            </div>
          </div>  
            
         <div class="control-group">
            <label for="inputError" class="control-label">Address</label>
            <div class="controls">
              <input type="text" id="" name="address" value="<?php echo set_value('address'); ?>">
              <!--<span class="help-inline">Address</span>-->
            </div>
          </div>  
            
          <div class="control-group">
            <label for="inputError" class="control-label">Telephone</label>
            <div class="controls">
              <input type="text" id="" name="telephone" value="<?php echo set_value('telephone'); ?>">
              <!--<span class="help-inline">Telephone</span>-->
            </div>
          </div>  
            
          <div class="control-group">
            <label for="inputError" class="control-label">Activities</label>
            <div class="controls">
              <input type="text" id="" name="activities" value="<?php echo set_value('activities'); ?>">
              <!--<span class="help-inline">Cost Price</span>-->
            </div>
          </div>
            
            
          
        <?php
            echo '<div class="control-group">';
            echo '<label for="category_id" class="control-label">Category</label>';
            echo '<div class="controls">';
              //echo form_dropdown('manufacture_id', $options_manufacture, '', 'class="span2"');

            echo form_dropdown('category_id', $options_category, set_value('category_id'), 'class="span2"');

            echo '</div>';
            echo '</div">';
          ?>
          
          <div class="form-actions">
            <button class="btn btn-primary" type="submit">Save changes</button>
            <button class="btn" type="reset">Cancel</button>
          </div>
        </fieldset>

      <?php echo form_close(); ?>

    </div>
     