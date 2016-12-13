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
          <?php echo $result; ?>
          <br/>
   
          <a  href="<?php echo site_url("admin").'/'?>Statistics/getnames" class="btn btn-success">Get Email Names</a>
          <div class="divider"/>
          <a  href="<?php echo site_url("admin").'/'?>Mailer/sendmailattach" class="btn btn-success">Send Email with Attachment</a>
          <br/>
          
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
            if (isset($Emails)){
                if (is_array($Emails)|| ($count_emails > 1 ))
                {
                  $options_emails = array();    
                  foreach ($Emails as $array) {
                      foreach ($array as $key => $value) {
                        $options_emails[$key] = $key;
                      }
                      break;
                  } 
                }else
                    $options_emails=$Emails;
            }

              echo form_open('admin/Emails', $attributes);
     
              echo form_label('Search:', 'search_string');
              echo form_input('search_string', $search_string_selected, 'style="width: 170px;
height: 26px;"');

              echo form_label('Filter by owner:', 'owner_id');
              echo form_dropdown('owner_id', $Email_Owner, $owner_selected, 'class="span2"');
              
              echo form_label('Filter by Source:', 'source_id');
              echo form_dropdown('source_id', $Email_Source, $source_selected, 'class="span2"');
              
              echo form_label('Filter by Company:', 'company_id');
              echo form_dropdown('company_id', $Companies, $company_selected, 'class="span2"');

              echo form_label('Order by:', 'order');
              echo form_dropdown('order', $options_emails, $order, 'class="span2"');

              $data_submit = array('name' => 'mysubmit', 'class' => 'btn btn-primary', 'value' => 'Go');

              $options_order_type = array('Asc' => 'Asc', 'Desc' => 'Desc');
              echo form_dropdown('order_type', $options_order_type, $order_type_selected, 'class="span1"');

              echo form_submit($data_submit);
              echo '<a href="'.site_url("admin").'/Emails/clear'.'" class="btn btn-primary" >Clear</a>';
              echo form_close();
            ?>

          </div>

          <table class="table table-striped table-bordered table-condensed">
            <thead>
              <tr>
                <th class="header">#</th>
                <div class="btn-inlinec pull-left">
                  <th class="yellow header headerSortDown">Email 
                     <?php 
                        if (isset($unVerfied_Emails)&&!empty($unVerfied_Emails))
                        {    
                           echo '<a href="'.site_url("admin").'/Emails/export/unverfied'.'" class="btn btn-primary" >Export</a>';
                           echo '<a href="'.site_url("admin").'/Emails/export/unverfied/url'.'" class="btn btn-primary" >Ex URL</a>';
                        }else{
                           echo '<a href="'.site_url("admin").'/Emails/export/verfied'.'" class="btn btn-primary" >Export</a>';
                           echo '<a href="'.site_url("admin").'/Emails/export/verfied/url'.'" class="btn btn-primary" >Ex URL</a>';
                        }
                      ?>
                  </th>
                 </div>
                
                <!-- <th class="green header">Country</th> -->
                <th class="red header">Rank</th>
                
                <?php  echo '<div class="btn-inlinec pull-left">
                              <th class="red header">Owner
                               <a href="'.site_url("admin").'/Owner'.'" class="btn btn-primary" >View</a>  
                              </th>  
                              
                              <th class="red header">Source
                                <a href="'.site_url("admin").'/source'.'" class="btn btn-primary" >View</a>  
                              </th>
                              
                              <th class="red header">Company
                                <a href="'.site_url("admin").'/company'.'" class="btn btn-primary" >View</a>  
                              </th>
                             </div>';       
                ?>
                
                <th class="red header">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php
              //$id=0;
              
              if (isset($Emails)){
                  if (is_array($Emails))
                  {
                      foreach($Emails as $row)
                      {
                          //$id++;
                        echo '<tr>';
                        echo '<td>'.$row['email_id'].'</td>';
                        echo '<td>'.$row['email'].'</td>';
                        //echo '<td>'.$row['country'].'</td>';
                        echo '<td>'.$row['rank'].'</td>';
                        echo '<td>'.$row['owner_id'].'</td>';
                        echo '<td>'.$row['source_id'].'</td>';
                        echo '<td>'.$row['company_id'].'</td>';
                        echo '<td class="crud-actions">
                          <a href="'.site_url("admin").'/emails/open/'.$row['email_id'].'" class="btn btn-info">view & edit</a>  
                          <a href="'.site_url("admin").'/emails/delete/'.$row['email_id'].'" class="btn btn-danger">delete</a>
                        </td>';
                        echo '</tr>';
                      }
                  }
              }
              ?>      
            </tbody>
          </table>
          <?php echo '<div class="pagination">'.$this->pagination->create_links().'</div>'; ?>
          <!-- <?php echo '<div class="pagination">'.$all_pages. $previous_page.$next_page.'</div>'; ?> -->

      </div>
    </div>