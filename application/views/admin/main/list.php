 
<script type="text/javascript">
$(document).ready(function () {
  setInterval(auto_refresh_function, 200000);
});
        function auto_refresh_function() {
          $('#ref').load('<?php echo base_url(); ?>index.php/admin/main');
        }
</script> 


<div id="ref">
        
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
          <a  href="<?php echo site_url("admin").'/'.$this->uri->segment(2); ?>/add" class="btn btn-success">Add a new</a>
        </h2>
      </div>
      
      
        
          <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          <h1 class="page-header">Dashboard</h1>

         </div>
          
      <div class="row">
        <div class="span12 columns">
            <div class="well"></div>
          
              
              <table class="table table-striped table-bordered table-condensed">
            <thead>
              <tr>
                <th class="header">Ntv Emails Count</th>
                <th class="yellow header headerSortDown">Companies Count</th>
                <th class="green header">count sources</th>
                <th class="red header">V Emails Count</th>
                <th class="red header">Mailer log Count</th>
                <th class="red header">count_sent_ae</th>
                <th class="red header">Mailer schedule log count</th>
                <th class="red header">email_sent_today</th>
                
                
              </tr>
            </thead>
            <tbody>
              <?php
                      //$id++;
                    echo '<tr>';
                    echo '<td>'.$count_ntvemails.'</td>';
                    echo '<td>'.$count_companies.'</td>';
                    echo '<td>'.$count_sources.'</td>';
                    echo '<td>'.$count_vemails.'</td>';
                    echo '<td>'.$count_mailer_log.'</td>';
                    echo '<td>'.$count_sent_ae.'</td>';
                    echo '<td>'.$count_mailer_schedule_log.'</td>';
                    echo '<td>'.$email_sent_today.'</td>';
                    echo '</tr>';                 
              ?>      
            </tbody>
            
            <thead>
              <tr>
                <th class="header">count emails no company</th>
                <th class="yellow header headerSortDown">count_scheculed_sa</th>
               
                <th class="red header">count_sent_sa</th>
                <th class="red header">count_scheculed_qa</th>
                <th class="red header">count_sent_qa</th>
                <th class="red header">count_scheculed_kw</th>
                <th class="red header">count_sent_kw</th>
                <th class="red header">count_scheculed_ae</th>
                
              </tr>
            </thead>
            <tbody>
              <?php

                    echo '<tr>';
                    echo '<td>'.$count_emails_no_company_id.'</td>';
                    echo '<td>'.$count_scheculed_sa.'</td>';
                    
                    echo '<td>'.$count_sent_sa.'</td>';
                    echo '<td>'.$count_scheculed_qa.'</td>';
                    echo '<td>'.$count_sent_qa.'</td>';
                    echo '<td>'.$count_scheculed_kw.'</td>';
                    echo '<td>'.$count_sent_kw.'</td>';
                    echo '<td>'.$count_scheculed_ae.'</td>';
                    echo '</tr>';                 
              ?>      
            </tbody>
          </table>
            
        

           <table class="table table-striped table-bordered table-condensed">
              
                    <h2 style="text-align: center">Mailer schedule log Table</h2>
                   
              <thead>
                 <tr>
                 <?php
                     if (is_array($mailer_schedule_table_fields))
                      {
                          foreach($mailer_schedule_table_fields as $col_name)
                          {
                            echo '<th class="red header">'.$col_name.'</th>';                                              
                          }
                      }
                  ?> 
                 </tr>  
                </thead>
                <tbody>
            
                  <?php
                     if (is_array($mailer_schedule_log))
                      {
                          foreach($mailer_schedule_log as $row)
                          {
                            echo '<tr>';  
                            if (is_array($mailer_schedule_table_fields))
                             {
                                  foreach($mailer_schedule_table_fields as $col_name)
                                  {
                                    echo '<td>'.$row[$col_name].'</td>';                                              
                                  }
                             }  
                             echo '</tr>'; 
                          }
                      }
                  ?>
                         
                </tbody>
              </table>  
            
            <span class="divider">/</span>
  
            
            <table class="table table-striped table-bordered table-condensed">
              
                    <h2 style="text-align: center">Cron log Table</h2>
                   
              <thead>
                 <tr>
                 <?php
                     if (is_array($cron_table_fields))
                      {
                          foreach($cron_table_fields as $col_name)
                          {
                            echo '<th class="red header">'.$col_name.'</th>';                                              
                          }
                      }
                  ?> 
                 </tr>  
                </thead>
                <tbody>
                    <tr>
                  <?php
                     if (is_array($cron_log))
                      {
                          foreach($cron_log as $row)
                          {
                            echo '<tr>';  
                            if (is_array($cron_table_fields))
                             {
                                  foreach($cron_table_fields as $col_name)
                                  {
                                    echo '<td>'.$row[$col_name].'</td>';                                              
                                  }
                             }
                              echo '</tr>'; 
                          }
                      }
                  ?>
                  </tr>       
                </tbody>
              </table>  
       
            <table class="table table-striped table-bordered table-condensed">
                    <h2 style="text-align: center">Extraction Table</h2>    
              <thead>
                 <tr>
                 <?php
                     if (is_array($Extraction_table_fields))
                      {
                          foreach($Extraction_table_fields as $col_name)
                          {
                            echo '<th class="red header">'.$col_name.'</th>';                                              
                          }
                      }
                  ?> 
                 </tr>  
                </thead>
                <tbody>
                    <tr>
                  <?php
                     if (is_array($Extraction))
                      {
                          foreach($Extraction as $row)
                          {
                            echo '<tr>';  
                            if (is_array($Extraction_table_fields))
                             {
                                  foreach($Extraction_table_fields as $col_name)
                                  {
                                    echo '<td>'.$row[$col_name].'</td>';                                              
                                  }
                             }  
                             echo '</tr>'; 
                          }
                      }
                  ?>
                  </tr>       
                </tbody>
              </table>
            
            
              <table class="table table-striped table-bordered table-condensed">
              
                    <h2 style="text-align: center">Mailer Scheduled Table</h2>
                   
              <thead>
                 <tr>
                 <?php
                     if (is_array($mailer_scheduled_table_fields))
                      {
                          foreach($mailer_scheduled_table_fields as $col_name)
                          {
                            echo '<th class="red header">'.$col_name.'</th>';                                              
                          }
                      }
                  ?> 
                 </tr>  
                </thead>
                <tbody>
            
                  <?php
                     if (is_array($mailer_scheduled))
                      {
                          foreach($mailer_scheduled as $row)
                          {
                            echo '<tr>';  
                            if (is_array($mailer_scheduled_table_fields))
                             {
                                  foreach($mailer_scheduled_table_fields as $col_name)
                                  {
                                    echo '<td>'.$row[$col_name].'</td>';                                              
                                  }
                             }  
                             echo '</tr>'; 
                          }
                      }
                  ?>
                         
                </tbody>
              </table>
            
          <div class="row placeholders">
            <div class="col-xs-6 col-sm-3 placeholder">
              <img src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" width="200" height="200" class="img-responsive" alt="Generic placeholder thumbnail">
              <h4>Label</h4>
              <span class="text-muted">Something else</span>
            </div>
            <div class="col-xs-6 col-sm-3 placeholder">
              <img src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" width="200" height="200" class="img-responsive" alt="Generic placeholder thumbnail">
              <h4>Label</h4>
              <span class="text-muted">Something else</span>
            </div>
            <div class="col-xs-6 col-sm-3 placeholder">
              <img src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" width="200" height="200" class="img-responsive" alt="Generic placeholder thumbnail">
              <h4>Label</h4>
              <span class="text-muted">Something else</span>
            </div>
            <div class="col-xs-6 col-sm-3 placeholder">
              <img src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" width="200" height="200" class="img-responsive" alt="Generic placeholder thumbnail">
              <h4>Label</h4>
              <span class="text-muted">Something else</span>
            </div>
          </div>
        
      

      </div>
    </div>
  </div>    
    