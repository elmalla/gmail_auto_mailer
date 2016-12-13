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
           
            <div class="container">
                    <ul class="nav nav-pills nav-justified">
                            <li class=""><a href="#section-1" data-toggle="tab">Section 1</a></li>
                            <li class="active"><a href="#section-2" data-toggle="tab">Section 2</a></li>
                            <li><a href="#section-3" data-toggle="tab">Section 3</a></li>
                    </ul>

                    <div class="tab-content">
                            <div class="tab-pane fade" id="section-1">
                                    <p>This is section 1 content</p>
                            </div>
                            <div class="tab-pane fade active in" id="section-2">
                                    <p>This is section 2 content</p>
                            </div>
                            <div class="tab-pane fade" id="section-3">
                                    <p>This is section 3 content</p>
                            </div>
                    </div>
            </div>
	
            
          </div>


          

       </div>
    </div>