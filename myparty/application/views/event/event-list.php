
<div class="tables">
    <h2 class="title1"><?= $title; ?></h2>
    <?= $this->session->flashdata('msg'); ?>
    <div class="bs-example widget-shadow"> 
        
        <table class="table table-bordered" id="example"> 
            <thead> 
                <tr> 
                    <th>#</th>
                    <th>Event Name</th>
                    <th>Start Date</th>
                    <th>Event Start Time</th>
                    <th>Event End Time</th>
                    <th>Event Venue</th>
                    <th>Accept Event Count</th>
                    <th>Reject Event Count</th>
                    <th>Action</th>
                </tr> 
            </thead> 
            <tbody> 
                <?php if (!empty($event)){                                                
                foreach ($event as $key => $value) { ?>
                    <tr>
                        <th scope="row"><?= ++$key; ?></th>
                       
                        <td><?= $value['event_name']; ?></td>
                        <td><?= $value['start_date']; ?></td>
                        <td><?= date("h:i a", strtotime($value['event_start_date'])); ?></td>
                        <td><?= date("h:i a", strtotime($value['event_end_date'])); ?></td>
                        <td><?= $value['event_venue']; ?></td>
                        <td><?= $value['accept_event_count']; ?></td>
                        <td><?= $value['reject_event_count']; ?></td>
                       
                        <td>
                              <a href="<?= base_url($link.$value['id']); ?>" class="btn btn-primary" ><i class="fa fa-eye"></i></a> &nbsp;

                              <?php $status = $value['event_status']; ?>
                                <?php if ($status == 1) { ?>
                                <a href="<?php echo base_url('admin/change_status') . '/id/' . $value['id']; ?>/event_tbl/status/0/event_list" class="btn btn-danger">BLOCK</a>
                                <?php } elseif ($status == 0) { ?>
                                <a href="<?php echo base_url('admin/change_status').'/id/'.$value['id']; ?>/event_tbl/status/1/event_list" class="btn btn-success">UNBLOCK</a>
                                <?php } ?>
                        </td>                        
                    </tr>
                <?php }
            } ?>
            </tbody> 
        </table>
    </div>
</div>

<style>
    .button {
    display: block;
    width: 115px;
    height: 25px;
    background: #4E9CAF;
    padding: 10px;
    text-align: center;
    border-radius: 5px;
    color: white;
    font-weight: bold;
}

</style>

