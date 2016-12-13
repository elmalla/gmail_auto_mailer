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
             
              <a  href="<?php echo site_url("admin").'/'?>Mailer/sendmail" class="btn btn-success">Send Email</a>
              <div class="divider"/>
              <a  href="<?php echo site_url("admin").'/'?>Mailer/sendmailattach" class="btn btn-success">Send Email with Attachment</a>
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
                <th class="green header">Emails count</th>
                
                
              </tr>
            </thead>
            <tbody>
              <?php
                      //$id++;
                    echo '<tr>';
                 
                    echo '<td>'.$emails_count.'</td>';
                    echo '</tr>';                 
              ?>      
            </tbody>
            
             <thead>
              <tr>
                <th class="header">Debugging Info</th>
           
              </tr>
            </thead>
            <tbody>
              <?php

                    echo '<tr>';
                    echo '<td>'.$debug.'</td>';
                    //echo '<td>'.$counts['companies_without_emails_count'].'</td>';
         
                    echo '</tr>';                 
              ?>      
            </tbody>
          </table>
    
          
          <table class="table table-striped table-bordered table-condensed">
          <thead>
              <tr>
                <th class="header">Email ID</th>
                <th class="header">Email sent</th>   
              </tr>
            </thead>
            <tbody>
              <?php
                 if (is_array($emails_sent)&& isset($emails_sent))
                  {
                      foreach($emails_sent as $key=>$value)
                      {   
                            echo '<tr>';  
                            echo '<td>'.$key.'</td>';
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
                <th class="header">Email ID</th>
                <th class="header">Email Failed</th>   
              </tr>
            </thead>
            <tbody>
              <?php
                 if (is_array($emails_nt_sent)&& isset($emails_nt_sent))
                  {
                      foreach($emails_nt_sent as $key=>$value)
                      {   
                            echo '<tr>';  
                            echo '<td>'.$key.'</td>';
                            echo '<td>'.$value.'</td>';
                            echo '</tr>';  
                      }
                  }
              ?>      
            </tbody>
          </table>  
            
      </div>
    </div>