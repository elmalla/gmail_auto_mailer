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
             <table class="table table-striped table-bordered table-condensed">
            <thead>
              <tr>
                <th class="header">email_v_count</th>
                <th class="yellow header headerSortDown">email_nv_count</th>
                <th class="green header">email_merged_count</th>
                <th class="red header">email_to_mailer_count</th>
                <th class="red header">email_sent_count</th>
                <th class="red header">emails_sent_earlier</th>
                <th class="red header">emails_scheduled_next_run</th>
                <th class="red header">N/A</th>
      
              </tr>
            </thead>
            <tbody>
              <?php
                   
                    echo '<tr>';
                    echo '<td>'.$counts['email_v_count'].'</td>';
                    echo '<td>'.$counts['email_nv_count'].'</td>';
                    echo '<td>'.$counts['email_merged_count'].'</td>';
                    echo '<td>'.$counts['email_to_mailer_count'].'</td>';
                    echo '<td>'.$counts['email_sent_count'].'</td>';
                    echo '<td>'.$counts['emails_sent_earlier'].'</td>';
                    echo '<td>'.$counts['emails_scheduled_next_run'].'</td>';
                    echo '<td>'.'N/A'.'</td>';
                    echo '</tr>';                 
              ?>      
            </tbody>
           </table> 
            
            <table class="table table-striped table-bordered table-condensed">
             <thead>
              <tr>
                <th class="header">Debugging Info</th>
              </tr>
              <tr>
                <th class="header">Email ID</th>
                <th class="header">Debug Info</th>   
              </tr>
            </thead>
            <tbody>
               <?php
                 if (is_array($emails_debug_info))
                  {
                      foreach($emails_debug_info as $key=>$value)
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
            
          <table class="table table-striped table-bordered table-condensed">
          <thead>
              <tr>
                <th class="header">Email ID</th>
                <th class="header">Email sent earlier</th>   
              </tr>
            </thead>
            <tbody>
              <?php
                 if (is_array($emails_sent_earlier)&& isset($emails_sent_earlier))
                  {
                      foreach($emails_sent_earlier as $key=>$value)
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
                <th class="header">Debug</th>
                
              </tr>
            </thead>
            <tbody>
              <?php
                 if (is_array($debug)&& isset($debug))
                  {
                      foreach($debug as $data)
                      {  
                         if (is_array($data) )
                         {
                           foreach($data as $value)
                           {
                             echo '<tr>';
                             //echo '<td>'.$key.'</td>';
                             if (is_array($value))
                             {
                                 foreach($value as $key=>$val)
                                 {
                                   echo '<td>'.$key.'</td>';   
                                   echo '<td>'.$val.'</td>';//echo '<td>'.implode(',',$value).'</td>';  
                                 }
                             } 
                             else
                               echo '<td>'.$value.'</td>';  
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