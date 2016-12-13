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
             
              <a  href="<?php echo site_url("admin").'/'?>Statistics/getnames" class="btn btn-success">Get Email Names</a>
              <div class="divider"/>
              <a  href="<?php echo site_url("admin").'/'?>Mailer/sendmailattach" class="btn btn-success">Stat2 </a>
              <br/>
          
        </h2>
      </div>
      
      <div class="row">
        <div class="span12 columns">
          <div class="well">
          
          </div>
             <?php        
                ?>


              <table class="table table-striped table-bordered table-condensed">
                <thead>
                  <tr>
                    <th class="header">Name not extracted from nv Email</th>
                    <th class="yellow header headerSortDown">Name not extracted from v Email</th>
                    <th class="green header">Name extracted from nv Emails</th>
                    <th class="red header">Name extracted from v Emails</th>
                    <th class="red header">Name extracted repeated nv Emails</th>
                    <th class="red header">Name extracted repeated v Emails</th>
                    <th class="red header">Name extracted unique nv Emails</th>
                    <th class="red header">Name extracted unique v Emails</th>


                  </tr>
                </thead>
                <tbody>
                  <?php
                          //$id++;
                        echo '<tr>';
                        echo '<td>'.$counters['name_nt_extracted_nv_count'].'</td>';
                        echo '<td>'.$counters['name_nt_extracted_v_count'].'</td>';
                        echo '<td>'.$counters['name_extracted_nv_count'].'</td>';
                        echo '<td>'.$counters['name_extracted_v_count'].'</td>';
                        echo '<td>'.$counters['name_extracted_repeated_nv_count'].'</td>';
                        echo '<td>'.$counters['name_extracted_repeated_v_count'].'</td>';
                        echo '<td>'.$counters['name_extracted_unique_nv_count'].'</td>';
                        echo '<td>'.$counters['name_extracted_unique_v_count'].'</td>';
                        echo '</tr>';                 
                  ?>      
                </tbody>

                 <thead>
                  <tr>
                    <th class="header">DB Insert count</th>
                    <th class="yellow header headerSortDown">Insert Loop</th>
                    <th class="red header">Array from v Emails</th>
                    <th class="red header">Array from nv Emails</th>
                    <th class="red header">Array from all Emails</th>
                    <th class="red header">Em</th>
                    <th class="red header">Em</th>
                    <th class="red header">Em</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  
                        echo '<tr>';
                        echo '<td>'.$counters['insert_count'].'</td>';
                        echo '<td>'.$counters['insert_loop'].'</td>';
                        echo '<td>'.$counters['unique_first_v_names_count'].'</td>';
                        echo '<td>'.$counters['unique_first_nv_names_count'].'</td>';
                        echo '<td>'.$unique_first_names_count.'</td>';
                        echo '<td>'.'N/A'.'</td>';
                        echo '<td>'.'N/A'.'</td>';
                        echo '<td>'.'N/A'.'</td>';
                        echo '</tr>';                 
                  ?>      
                </tbody>
              </table>


              <table class="table table-striped table-bordered table-condensed">
              <thead>
                  <tr>
                    <th class="header">Name</th>
                    <th class="header">Count</th>
                    <th class="header">Email</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                     if (is_array($unique_first_names))
                      {
                          foreach($unique_first_names as $key=>$value)
                          {
                            echo '<tr>';
                            echo '<td>'.$key.'</td>';
                            echo '<td>'.$value['count'].'</td>';
                            echo '<td>'.$value['email'].'</td>';
                            echo '</tr>';                 
                          }
                      }
                  ?>      
                </tbody>
              </table>  

              <table class="table table-striped table-bordered table-condensed">
              <thead>
                  <tr>
                    <th class="header">Emails without names</th>

                  </tr>
                </thead>
                <tbody>
                  <?php
                     if (is_array($unique_emails_wout_names))
                      {
                          foreach($unique_emails_wout_names as $key=>$value)
                          {
                            echo '<tr>';  
                            echo '<td>'.$value.'</td>';
                            echo '</tr>';                 
                          }
                      }
                  ?>      
                </tbody>
              </table> 


      </div>
    </div>