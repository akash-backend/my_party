
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

    <div class="row">
        <div class="form-three widget-shadow">                                  
            <div class="row">
                <div class="col-md-12">
                    <h3>Basic Detail</h3><br>
                </div>
                
                <div class="col-md-12">                    
                    <dl class="dl-horizontal">
                        
                        <dt>Event Name</dt> <dd><?= $event_detail['event_name']; ?></dd>
                        <dt>Venue </dt> <dd><?= $event_detail['event_venue']; ?></dd>
                        <dt>Event Description</dt> <dd> <?= $event_detail['event_description']; ?></dd>
                        <dt>Start Date </dt> <dd> <?= $event_detail['start_date']; ?></dd> 
                        <dt>Accept Count</dt> <dd> <?= $event_detail['accept_event_count']; ?></dd>
                        <dt>Reject Count</dt> <dd> <?= $event_detail['reject_event_count']; ?></dd>
                        <dt>Event Start Date</dt> <dd> <?= date("h:i a", strtotime($event_detail['event_start_date'])); ?></dd>  
                        <dt>Event End Date</dt> <dd> <?= date("h:i a", strtotime($event_detail['event_end_date'])); ?></dd>  

                    </dl>
                </div>
                    
          
        </div>        
    </div>
</div>





