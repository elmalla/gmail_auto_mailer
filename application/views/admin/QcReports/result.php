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
          
          <br/>
                
          <a  href="<?php echo site_url("admin").'/'.$this->uri->segment(2); ?>/add" class="btn btn-success">TO be Done</a>
        </h2>
      </div>
      
      <div class="row">
        <div class="span12 columns">
          <div class="well">
          
          </div>
  
          <table class="table table-striped table-bordered table-condensed">
            <thead>
              <tr>
                <th class="header">Emails Extracted</th>
                <th class="header">Empty</th>
                <th class="green header">Lines count</th>
                <th class="red header">Empty Lines</th>
                <th class="red header">URL updated</th>
                <th class="red header">Companies Inserted</th>
                <th class="red header">Emails Verified</th>
                <th class="red header">Emails not Verified</th>
  
              </tr>
            </thead>
            <tbody>
              <?php
                      //$id++;
                    echo '<tr>';
                    echo '<td>'.$counts['emails_extracted_count'].'</td>';
                     echo '<td>'.'0'.'</td>';
                    echo '<td>'.$counts['lines_count'].'</td>';
                    echo '<td>'.$counts['non_empty_lines_count'].'</td>';
                    echo '<td>'.$counts['companies_url_updated_count'].'</td>';
                    echo '<td>'.$counts['companies_inserted_count'].'</td>';
                    echo '<td>'.$counts['emails_verified_count'].'</td>';
                    echo '<td>'.$counts['emails_ntverified_count'].'</td>';
                    echo '</tr>';                 
              ?>      
            </tbody>
            
             <thead>
              <tr>
                 <th class="header">non_empty_data_percentage</th>
                <th class="header">valuable_data_percentage</th>
                <th class="yellow header headerSortDown">emails_wformated_percentage</th>
               
                <th class="red header">emails_bformated_percentage</th>
                <th class="red header">total_emails_percentage</th>
                <th class="red header">new_emails_inserted_percentage</th>
                <th class="red header">new_validated_emails_inserted_percentage</th>
                <th class="red header">new_nvalidated_emails_inserted_percentage</th>
                <th class="red header">old emails percentage </th>
                
              </tr>
            </thead>
            <tbody>
              <?php

                    echo '<tr>';
                   
                    echo '<td>'.$statistics['non_empty_data_percentage'].'</td>';
                    echo '<td>'.$statistics['valuable_data_percentage'].'</td>';
                    
                    echo '<td>'.$statistics['emails_wformated_percentage'].'</td>';
                    echo '<td>'.$statistics['emails_bformated_percentage'].'</td>';
                    echo '<td>'.$statistics['total_emails_percentage'].'</td>';
                    echo '<td>'.$statistics['new_emails_inserted_percentage'].'</td>';
                    echo '<td>'.$statistics['new_validated_emails_inserted_percentage'].'</td>';
                    echo '<td>'.$statistics['new_nvalidated_emails_inserted_percentage'].'</td>';
                    echo '<td>'.$statistics['old_emails_percentage'].'</td>';
                    echo '</tr>';                 
              ?>      
            </tbody>
          </table>
            
            
          <table class="table table-striped table-bordered table-condensed">
          <thead>
              <tr>
                <th class="header">#</th>
                <th class="yellow header headerSortDown">CSV line</th>
                <th class="yellow header headerSortDown">CSV line Empty</th>
                <th class="yellow header headerSortDown">CSV First section Empty</th>
                <th class="yellow header headerSortDown">Email</th>
                <th class="yellow header headerSortDown">Email wFormated</th>
                <th class="yellow header headerSortDown">Email Validated</th>
                
                <th class="yellow header headerSortDown">Email v id</th>
                <th class="yellow header headerSortDown">Email ntV id</th>
                
                <th class="yellow header headerSortDown">Company v wF id DB</th>
                 <th class="yellow header headerSortDown">Company w id CDB</th>

                        
                        
                <th class="yellow header headerSortDown">company w url match status</th>
                <th class="yellow header headerSortDown">Company w url DB</th>

                <th class="yellow header headerSortDown">Email v Newly inserted</th>
                <th class="yellow header headerSortDown">Email ntV Newly inserted</th>
                
                <th class="yellow header headerSortDown">Email bFormated</th>
                <th class="yellow header headerSortDown">Email Validated</th>
                <th class="yellow header headerSortDown">Email v id</th>
                <th class="yellow header headerSortDown">Email ntV id</th>
                
                <th class="yellow header headerSortDown">Company v bF id DB</th>
                 <th class="yellow header headerSortDown">Company b id CDB</th>
                <th class="yellow header headerSortDown">company b url match status</th>
                <th class="yellow header headerSortDown">Company b url DB</th>
                
                <th class="yellow header headerSortDown">Email v Newly inserted</th>
                <th class="yellow header headerSortDown">Email ntV Newly inserted</th>
                <th class="yellow header headerSortDown">Company</th>
                <th class="yellow header headerSortDown">Address</th>
                <th class="yellow header headerSortDown">Country</th>
                <th class="yellow header headerSortDown">Telephone</th>
                <th class="yellow header headerSortDown">Category Name</th>
                <th class="yellow header headerSortDown">URL Funtion</th>
                <th class="yellow header headerSortDown">URL</th>
                <th class="yellow header headerSortDown">Source ID</th>
                
                
              </tr>
            </thead>
            
            <script type="text/javascript">

                // Ajax post
                $(document).ready(function() {
                $(".submit").click(function(event) {
                event.preventDefault();
                var user_name = $("input#name").val();
                var password = $("input#pwd").val();
                jQuery.ajax({
                type: "POST",
                url: "<?php echo base_url(); ?>" + "index.php/ajax_post_controller/user_data_submit",
                dataType: 'json',
                data: {name: user_name, pwd: password},
                success: function(res) {
                if (res)
                {
                // Show Entered Value
                jQuery("div#result").show();
                jQuery("div#value").html(res.username);
                jQuery("div#value_pwd").html(res.pwd);
                }
                }
                });
                });
                });
            </script>
            
            <tbody>
              <?php
                 if (is_array($raw_data))
                  {
                      foreach($raw_data as $row)
                      {
                        echo '<tr>';
                        echo '<td>'.$row['line_number'].'</td>';
                        echo '<td>'.$row['csv_line'].'</td>';
                        echo '<td>'.$row['csv_line_status'].'</td>';
                        echo '<td>'.$row['csv_first_section'].'</td>';
                         echo '<td>'.$row['email'].'</td>';
                        echo '<td>'.$row['email_w'].'</td>';
                        echo '<td>'.$row['e_w_validation'].'</td>';
                        echo '<td>'.$row['vEmail_w_id_DB'].'</td>';
                        echo '<td>'.$row['ntvEmail_w_id_DB'].'</td>';
                        
                       
                        
                        echo '<td>'.$row['vCompany_w_id_DB'].'</td>';
                        echo '<td>'.$row['Company_w_id_CDB'].'</td>';
                        echo '<td>'.$row['company_w_url_match_status'].'</td>';
                        echo '<td>'.$row['vCompany_w_url_DB'].'</td>';
                        
                        echo '<td>'.$row['vEmail_w_new'].'</td>';
                        echo '<td>'.$row['ntvEmail_w_new'].'</td>';
                        echo '<td>'.$row['email_b'].'</td>';
                        echo '<td>'.$row['e_b_validation'].'</td>';
                        echo '<td>'.$row['vEmail_b_id_DB'].'</td>';
                        echo '<td>'.$row['Company_b_id_CDB'].'</td>';
                        echo '<td>'.$row['ntvEmail_b_id_DB'].'</td>';
                        
                        echo '<td>'.$row['vCompany_b_id_DB'].'</td>';
                        echo '<td>'.$row['company_b_url_match_status'].'</td>';
                        echo '<td>'.$row['vCompany_b_url_DB'].'</td>';
                        
                        echo '<td>'.$row['vEmail_b_new'].'</td>';
                        echo '<td>'.$row['ntvEmail_b_new'].'</td>';
                        echo '<td>'.$row['company_name'].'</td>';
                        echo '<td>'.$row['address'].'</td>';
                        echo '<td>'.$row['country'].'</td>';
                        echo '<td>'.$row['telephone'].'</td>';
                        echo '<td>'.$row['category_name'].'</td>';
                        echo '<td>'.$row['url_f'].'</td>';
                        echo '<td>'.$row['url'].'</td>';
                        echo '<td>'.$row['source_id'].'</td>';
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