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
              
       <style>
   /* All HTML5 progress enabled browsers */
                #progressor {

                        /* Turns off styling - not usually needed, but good to know. */
                        appearance: none;
                        -moz-appearance: none;
                        -webkit-appearance: none;
                        /* gets rid of default border in Firefox and Opera. */
                        border: solid darkslategray 5px;
                        border-radius: 10px;
                        /* Dimensions */
                        width: 320px;
                        height: 25px;
                }


                /*
                 * Background of the progress bar background
                 */

                /* Chrome */
                #progressor::-webkit-progress-bar {
                        background: darkslategray;
                }

                /*
                 * Background of the progress bar value
                 */

                /* Firefox */
                #progressor::-moz-progress-bar {
                        border-radius: 5px;
                        background-image: -moz-linear-gradient(
                                center bottom,
                                rgb(43,194,83) 37%,
                                rgb(84,240,84) 69%
                        );
                }

                /* Chrome */
                #progressor::-webkit-progress-value {
                        border-radius: 5px;
                        background-image: -webkit-gradient(
                                linear,
                                left bottom,
                                left top,
                                color-stop(0, rgb(43,194,83)),
                                color-stop(1, rgb(84,240,84))
                        );
                        background-image: -webkit-linear-gradient(
                                center bottom,
                                rgb(43,194,83) 37%,
                                rgb(84,240,84) 69%
                        );
                }

        </style>
        <script>
        var source = 'THE SOURCE';
         
        function start_task()
        {
            source = new EventSource('admin_mailer.php');
             
            //a message is received
            source.addEventListener('message' , function(e) 
            {
                var result = JSON.parse( e.data );
                 
                add_log(result.message);
                 
                 
                if(e.lastEventId == 'CLOSE')
                {
                    add_log('Received CLOSE closing');
                    source.close();
                    //document.getElementById('progressor').style.width = "100%";
                    var pBar = document.getElementById('progressor');
                    pBar.value = pBar.max;
                }
                else {
                    //document.getElementById('progressor').style.width = result.progress + "%";
                    var pBar = document.getElementById('progressor');
                    pBar.value = result.progress;
                    var perc = document.getElementById('percentage');
                    perc.innerHTML  = result.progress  + "%";
                    perc.style.width = (Math.floor(pBar.clientWidth * (result.progress/100)) + 15) + 'px';
                }
            });
             
            source.addEventListener('error' , function(e)
            {
                add_log('Error occured');
                 
                //kill the object ?
                source.close();
            });
        }
         
        function stop_task()
        {
            source.close();
            add_log('Interrupted');
        }
         
        function add_log(message)
        {
            var r = document.getElementById('results');
            r.innerHTML += message + '<br>';
            r.scrollTop = r.scrollHeight;
        }
        </script>
    
    <div class="row">
        <div class="span12 columns">
        <br />
        <input type="button" onclick="start_task();"  value="Start Long Task" />
        <input type="button" onclick="stop_task();"  value="Stop Task" />
        <br />
        <br />
         
        Results
        <br />
        <div id="results" style="border:1px solid #000; padding:10px; width:300px; height:250px; overflow:auto; background:#eee;"></div>
        <br />
       
        <progress id='progressor' value="0" max='100' style=""></progress>  
         <span id="percentage" style="text-align:right; display:block; margin-top:5px;">0</span>
<!--
        <div style="border:1px solid #ccc; width:300px; height:20px; overflow:auto; background:#eee;">
            <div id="progressor" style="background:#07c; width:0%; height:100%;"></div>
        </div>
         -->
     </div>
    </div>
