
<style type="text/css">
.iii img {
    width: 100%; max-height:300px;
 }
</style>
<div class="forms">
    <h2 class="title1"><?= $title; ?></h2>

    <div class="alert alert-dismissible fade" role="alert"><span id="msg"></span>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>

 <!--    <div class="row">
        <div class="form-three widget-shadow">                                  
            <div class="row">
                <div class="col-md-12">
                    <h3>Basic Detail</h3><br>
                </div>
                <div class="col-md-4">
                    <div>
                        <?php
                        if(!empty($user['user_image']))
                            {
                        ?>
                            <img class="img-responsive" src="<?php echo base_url('/assets/userfile/profile/'.$user['user_image']); ?>" height="250px" width="200px">
                        <?php
                        }
                        ?>
                        <br/><br/>
                    </div >
                </div>
                <div class="col-md-8">                    
                    <dl class="dl-horizontal">
                        <dt>User ID </dt> <dd><?= $user['id']; ?></dd>
                        <dt>Username</dt> <dd><?= $user['first_name'].' '.$user['last_name']; ?></dd>
                        <dt>Phone </dt> <dd><?= $user['mobile']; ?></dd>
                        <dt>Email </dt> <dd> <?= $user['email']; ?></dd>
                        <dt>Available Time </dt> <dd><?= date("h:i a", strtotime($user['start_time'])).' '.date("h:i a", strtotime($user['end_time'])) ; ?></dd>
                        <dt>Identity Tag</dt> <dd><?= $user['address']; ?></dd>
                        <dt>Description </dt> <dd><?= $user['description']; ?></dd>
                        </dl>
                </div>
                </div>
          
        </div>        
    </div> -->


      <div class="row">
        <div class="form-three widget-shadow">                                  
            <div class="row">
                <div class="col-md-12 text-center">
                    <div class="user-profile-pic">
                        <?php
                        if(!empty($user['user_image']))
                        {
                        ?>
                            <img class="img-responsive" src="<?php echo base_url('/assets/userfile/profile/'.$user['user_image']); ?>">
                        <?php
                        }
                        else
                        {
                        ?>
                            <img class="img-responsive" src="<?php echo base_url('/assets/images/admin.png'); ?>">
                        <?php
                        }
                        ?>
                    </div>
                    <div class="mt-2">
                        <h2 class="text-theme"><?= $user['first_name'].' '.$user['last_name']; ?></h2>
                        <p class="mt-1">My Party App user since December <?= date("F Y", strtotime($user['created_at'])); ?></p>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="mt-2">
                      <hr>
                      <p class="mt-1">
                        <img class="img-responsive new-image-pic" src="<?php echo base_url('/assets/icon/profile.png'); ?>" >
                        <?= $user['first_name'].' '.$user['last_name']; ?></p>
                      <hr>
                      <p class="mt-1"><img class="img-responsive new-image-pic" src="<?php echo base_url('/assets/icon/mob.png'); ?>" ><?= $user['mobile']; ?></p>
                      <hr>
                      <p class="mt-1"><img class="img-responsive new-image-pic" src="<?php echo base_url('/assets/icon/email.png'); ?>" ><?= $user['email']; ?></p>
                      <hr>
                      <p class="mt-1"><img class="img-responsive new-image-pic" src="<?php echo base_url('/assets/icon/time.png'); ?>" > <?= date("h:i a", strtotime($user['start_time'])).' '.date("h:i a", strtotime($user['end_time'])) ; ?></p>
                      <hr>
                      <p class="mt-1"><img class="img-responsive new-image-pic" src="<?php echo base_url('/assets/icon/location.png'); ?>" ><?= $user['address']; ?></p>
                      <hr>
                      <p class="mt-1"><img class="img-responsive new-image-pic" src="<?php echo base_url('/assets/icon/about.png'); ?>" ><?= $user['description']; ?></p>
                    </div>
                </div>    
            </div>
        </div>        
    </div>

    
</div>

