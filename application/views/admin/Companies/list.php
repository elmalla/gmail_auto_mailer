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
          <?php echo $count_companies; ?>
          <br/>
          <?php echo $page; ?>      
          <a  href="<?php echo site_url("admin").'/'?>Companies/add" class="btn btn-success">Add new Company</a>
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
             
            if (is_array($Companies)|| ($count_companies > 1) )
            {
              $options_companies = array();   
              foreach ($Companies as $array) {
                  foreach ($array as $key => $value) {
                    $options_companies[$key] = $key;
                  }
                  break;
              } 
            }else
                $options_companies=$Companies;
            //$option_companies =array("$Companies[1]"=>$Companies[1]);
            
            
            if (is_array($Countries)&& (count($Countries) >1 ))
            {
              $options_countries = array();    
              foreach ($Countries as $array) {
                  foreach ($array as $key => $value) {
                    $option_countries[$key] = $key;
                  }
                  break;
              } 
            
            }else
                $option_countries =array("$Countries[1]"=>$Countries[1]);
            
              echo form_open('admin/Companies', $attributes);
     
              echo form_label('Search:', 'search_string');
              echo form_input('search_string', $search_string_selected, 'style="width: 170px;
height: 26px;"');

              echo form_label('Filter by country:', 'country');
              echo form_dropdown('country', $option_countries, $country_selected, 'class="span2"');
              
              echo form_label('Filter by Category:', 'category_id');
              echo form_dropdown('category_id', $Categories, $category_selected, 'class="span2"');
              
         

              echo form_label('Order by:', 'order');
              echo form_dropdown('order', $options_companies, $order, 'class="span2"');

              $data_submit = array('name' => 'mysubmit', 'class' => 'btn btn-primary', 'value' => 'Go');

              $options_order_type = array('Asc' => 'Asc', 'Desc' => 'Desc');
              echo form_dropdown('order_type', $options_order_type, $order_type_selected, 'class="span1"');

              echo form_submit($data_submit);
 
              echo '<a href="'.site_url("admin").'/Companies/clear'.'" class="btn btn-primary" >Clear</a>';
              echo form_close();
            ?>

          </div>

          <table class="table table-striped table-bordered table-condensed">
            <thead>
              <tr>
                <th class="header">#</th>
                <th class="yellow header headerSortDown">Company</th>
                <!--<th class="yellow header headerSortDown">Address</th> -->
                <th class="yellow header headerSortDown">Country</th>
                <!--<th class="yellow header headerSortDown">Telephone</th> -->
                
                <?php  echo '<div class="btn-inline pull-left">
                              <th class="yellow header headerSortDown">url
                               <a href="'.site_url("admin").'/Companies/export'.'" class="btn btn-primary" >Export</a>  
                              </th>  
                              
                             </div>';       
                ?>
                
                
                <th class="yellow header headerSortDown">Activities</th>
                <!-- <th class="green header">Country</th> -->
                
                
                <?php  echo '<div class="btn-inline pull-left">
                              <th class="red header">Category
                               <a href="'.site_url("admin").'/Category'.'" class="btn btn-primary" >View</a>  
                              </th>  
                              
                             </div>';       
                ?>
                
                <th class="red header">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php
              //$id=0;
              if (is_array($Companies)|| ($count_companies > 0))
              {
                  foreach($Companies as $row)
                  {
                      //$id++;
                    echo '<tr>';
                    echo '<td>'.$row['company_id'].'</td>';
                    echo '<td>'.$row['company_name'].'</td>';
                    //echo '<td>'.$row['country'].'</td>';
                    //echo '<td>'.$row['address'].'</td>';
                    echo '<td>'.$row['country'].'</td>';
                    //echo '<td>'.$row['telephone'].'</td>';
                    echo '<td>'.$row['url'].'</td>';
                    echo '<td>'.$row['activities'].'</td>';
                    echo '<td>'.' '.'</td>';
                    echo '<td class="crud-actions">
                      <a href="'.site_url("admin").'/Companies/open/'.$row['company_id'].'" class="btn btn-info">view & edit</a>  
                      <a href="'.site_url("admin").'/Companies/delete/'.$row['company_id'].'" class="btn btn-danger">delete</a>
                    </td>';
                    echo '</tr>';
                  }
              }
              ?>      
            </tbody>
          </table>
          <?php echo '<div class="pagination">'.$this->pagination->create_links().'</div>'; ?>
          

       </div>
    </div>