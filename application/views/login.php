<?php include 'header.php' ?>

  <div class="container">

    <div class="row">
      <div class="span4 offset4 well">

        <legend>Please Sign In</legend>

        <?php if (isset($error) && $error): ?>
          <div class="alert alert-error">
            <a class="close" data-dismiss="alert" href="#">×</a>Incorrect Username or Password!
          </div>
        <?php endif; ?>

        
        <form action="<?=  base_url().'index.php/Login/login_user'?>" method="post">  
            
        <input type="text" id="email" class="span4" name="email" placeholder="Email Address">
        <input type="password" id="password" class="span4" name="password" placeholder="Password">

        <!--<label class="checkbox">
          <input type="checkbox" name="remember" value="1"> Remember Me
        </label>-->

        <button type="submit" name="submit" class="btn btn-info btn-block">Sign in</button>

        </form>
      </div>
    </div>

    <div class="row">
      <div class="span6 offset4">
        <p><strong>Away Please</strong> Keep your distance</p>

      </div>
    </div>
  </div>

<?php include 'footer.php' ?>