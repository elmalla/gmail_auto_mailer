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
          <br/>
          <?php echo $count_owner; ?>
          <br/>
          <?php echo $page; ?>      
          <a  href="<?php echo site_url("admin").'/'.$this->uri->segment(2); ?>/add" class="btn btn-success">Add new Owner</a>
        </h2>
      </div>
      
      <div class="row">
        <div class="span12 columns">
          <div class="well">
           
            <?php
           
            $attributes = array('class' => 'form-inline reset-margin', 'id' => 'myform');
           
            /* 
            $options_owner = array(0 => "all");
            foreach ($email_owner as $row)
            {
              $options_owner[$row['id']] = $row['owner'];
            }
            */
      
            //save the columns names in a array that we will use as filter         
            if (is_array($Owner)|| ($count_owner > ($page*$page_count)) )
            {
              $options_owner = array();    
              foreach ($Owner as $array) {
                  foreach ($array as $key => $value) {
                    $options_owner[$key] = $key;
                  }
                  break;
              } 
            }else
                $options_owner=$Owner;
            

              echo form_open('admin/Owner', $attributes);
     
              echo form_label('Search:', 'search_string');
              echo form_input('search_string', $search_string_selected, 'style="width: 170px;
height: 26px;"');
/*
              echo form_label('Filter by owner:', 'owner_id');
              echo form_dropdown('owner_id', $Email_Owner, $owner_selected, 'class="span2"');
              
              echo form_label('Filter by Source:', 'source_id');
              echo form_dropdown('source_id', $Email_Source, $source_selected, 'class="span2"');
              
              echo form_label('Filter by Company:', 'company_id');
              echo form_dropdown('company_id', $Company_Category, $company_selected, 'class="span2"');
*/
              echo form_label('Order by:', 'order');
              echo form_dropdown('order', $options_owner, $order, 'class="span2"');

              $data_submit = array('name' => 'mysubmit', 'class' => 'btn btn-primary', 'value' => 'Go');

              $options_order_type = array('Asc' => 'Asc', 'Desc' => 'Desc');
              echo form_dropdown('order_type', $options_order_type, $order_type_selected, 'class="span1"');

              echo form_submit($data_submit);

              echo form_close();
            ?>

          </div>

          <table class="table table-striped table-bordered table-condensed">
            <thead>
              <tr>
                <th class="header">#</th>
                <th class="yellow header headerSortDown">Owner</th>
                <!-- <th class="green header">Country</th> -->
                
                
                <th class="red header">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php
              //$id=0;
              if (is_array($Owner)|| ($count_owner > ($page*$page_count)))
              {
                  foreach($Owner as $row)
                  {
                      //$id++;
                    echo '<tr>';
                    echo '<td>'.$row['id'].'</td>';
                    echo '<td>'.$row['owner'].'</td>';
                   
                    echo '<td class="crud-actions">
                      <a href="'.site_url("admin").'/Owner/update/'.$row['id'].'" class="btn btn-info">view & edit</a>  
                      <a href="'.site_url("admin").'/Owner/delete/'.$row['id'].'" class="btn btn-danger">delete</a>
                    </td>';
                    echo '</tr>';
                  }
              }
              ?>      
            </tbody>
          </table>
          <?php echo '<div class="pagination">'.$this->pagination->create_links().'</div>'; ?>
          <!-- <?php echo '<div class="pagination">'.$all_pages. $previous_page.$next_page.'</div>'; ?> -->

      </div>
    </div>