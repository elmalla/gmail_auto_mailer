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
          <a  href="<?php echo site_url("admin").'/export'; ?>/show" class="btn btn-success">Add new Owner</a>
        </h2>
      </div>
      
      <div class="row">
        <div class="span12 columns">
          <div class="well">
           
            <?php
           
            $attributes = array('class' => 'form-inline reset-margin', 'id' => 'myform');
           
           
            //save the columns names in a array that we will use as filter         
            $options_files = array();    
            foreach ($ExportedFiles as $array) {
              foreach ($array as $key => $value) {
                $options_files[$key] = $key;
              }
              break;
            }

            echo form_open('admin/export/show', $attributes);
     
              echo form_label('Search:', 'search_string');
              echo form_input('search_string', $search_string_selected, 'style="width: 170px;
height: 26px;"');

              //echo form_label('Filter by manufacturer:', 'manufacture_id');
              //echo form_dropdown('manufacture_id', $options_manufacture, $manufacture_selected, 'class="span2"');

              echo form_label('Order by:', 'order');
              echo form_dropdown('order', $options_files, $order, 'class="span2"');

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
                <th class="yellow header headerSortDown">Name</th>
                <th class="green header">Path</th>
                <th class="red header">Size</th>
                <th class="red header">Date</th>
                
                <th class="red header">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $id=0;
              foreach($ExportedFiles as $row)
              {
                  $id++;
                echo '<tr>';
                echo '<td>'.$id.'</td>';
                echo '<td>'.$row['name'].'</td>';
                echo '<td>'.$row['server_path'].'</td>';
                echo '<td>'.$row['size'].'</td>';
                echo '<td>'.$row['date'].'</td>';
                
                echo '<td class="crud-actions">
                  <a href="'.site_url("admin").'/export/download/'.$row['name'].'" class="btn btn-info">download </a>
                  <a href="'.site_url("admin").'/export/open/'.$id.'" class="btn btn-info">view </a>    
                  <a href="'.site_url("admin").'/export/delete/'.$id.'" class="btn btn-danger">delete</a>
                </td>';
                echo '</tr>';
              }
              ?>      
            </tbody>
          </table>

          <?php echo '<div class="pagination">'.$this->pagination->create_links().'</div>'; ?>

      </div>
    </div>