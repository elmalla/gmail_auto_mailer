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
              
       
        <script>

            var source = new EventSource("<?php echo site_url('admin_mailer/loaddata');?>");

              source.addEventListener("message", function(e) {
               document.getElementById("status").innerHTML = "MESSAGE";showdata(e);
               }, false);

              source.addEventListener("open", function(e) {
               document.getElementById("status").innerHTML = "OPENED";
               }, false);

              source.addEventListener("error", function(e) {
               document.getElementById("status").innerHTML = e.readyState;
               if (e.readyState == EventSource.CLOSED) {
                document.getElementById("status").innerHTML = "CLOSED";
                 }
               }, false);
             } else {
              document.getElementById("status").innerHTML = "SSE not Supported";
             }
            }

            function showdata(Jdat)
            {
             var data = JSON.parse(Jdat);

             document.getElementById("results").innerHTML = data.name;
            }
 
        </script>
    
    <div class="row">
        <div class="span12 columns">
         <br />
         <div id="status" style="border:1px solid #000; padding:10px; width:300px; height:250px; overflow:auto; background:#eee;"></div>
         <br />
         <br />
         Results
        <br />
        <div id="results" style="border:1px solid #000; padding:10px; width:300px; height:250px; overflow:auto; background:#eee;"></div>
        <br />   
            
     </div>
    </div>
