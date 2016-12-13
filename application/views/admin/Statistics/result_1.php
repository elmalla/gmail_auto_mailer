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
             <?php        
                ?>

          
          <table class="table table-striped table-bordered table-condensed">
            <thead>
              <tr>
                <th class="header">Emails Extracted</th>
                <th class="yellow header headerSortDown">Companies Extracted</th>
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
                    echo '<td>'.$companies_count.'</td>';
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
                <th class="header">Companies without URl</th>
                <th class="yellow header headerSortDown">Companies without Emails</th>
               
                <th class="red header">Company Name from Email</th>
                <th class="red header">Category Inserted</th>
                <th class="red header">Companies not inserted</th>
                <th class="red header">Email bad formated</th>
                <th class="red header">Email duplicate in file</th>
                <th class="red header">Email already in DB </th>
                
              </tr>
            </thead>
            <tbody>
              <?php

                    echo '<tr>';
                    echo '<td>'.$counts['companies_without_url_count'].'</td>';
                    echo '<td>'.$counts['companies_without_emails_count'].'</td>';
                    
                    echo '<td>'.$counts['company_name_fr_email_count'].'</td>';
                    echo '<td>'.$counts['category_inserted_count'].'</td>';
                    echo '<td>'.$counts['companies_nt_inserted_count'].'</td>';
                    echo '<td>'.$counts['emails_bformated_count'].'</td>';
                    echo '<td>'.$counts['email_duplicate_in_file_count'].'</td>';
                    echo '<td>'.$counts['email_in_DB_count'].'</td>';
                    echo '</tr>';                 
              ?>      
            </tbody>
          </table>
            
            
          <table class="table table-striped table-bordered table-condensed">
          <thead>
              <tr>
                <th class="header">Email Duplicate_in DB</th>
                
              </tr>
            </thead>
            <tbody>
              <?php
                 if (is_array($emails_in_DB))
                  {
                      foreach($emails_in_DB as $key=>$value)
                      {
                        echo '<tr>';  
                        echo '<td>'.$value.'</td>';
                        echo '</tr>';                 
                      }
                  }
              ?>      
            </tbody>
          </table>  
            
          <table class="table table-striped table-bordered table-condensed">
          <thead>
              <tr>
                <th class="header">Email Duplicate_in_file</th>
                
              </tr>
            </thead>
            <tbody>
              <?php
                 if (is_array($emails_duplicate))
                  {
                      foreach($emails_duplicate as $key=>$value)
                      {
                        echo '<tr>';  
                        echo '<td>'.$value.'</td>';
                        echo '</tr>';                 
                      }
                  }
              ?>      
            </tbody>
          </table> 
            
          
          <table class="table table-striped table-bordered table-condensed">
          <thead>
              <tr>
                <th class="header">Email bad formated</th>
                
              </tr>
            </thead>
            <tbody>
              <?php
                 if (is_array($bformated_emails))
                  {
                      foreach($bformated_emails as $key=>$value)
                      {
                        foreach($value as $key=>$email)
                        {  
                          if ($email !=''){   
                            echo '<tr>';  
                            echo '<td>'.$email.'</td>';
                            echo '</tr>';  
                       }
                      }
                      }
                  
                  }
              ?>      
            </tbody>
          </table>

      </div>
    </div>