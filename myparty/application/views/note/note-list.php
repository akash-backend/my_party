
<div class="tables">
    <h2 class="title1"><?= $title; ?></h2>
    <?= $this->session->flashdata('msg'); ?>
    <div class="bs-example widget-shadow"> 
        
        <table class="table table-bordered" id="example"> 
            <thead> 
                <tr> 
                    <th>#</th>
                    <th>Event Name</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Venu</th>
                    <th>Description</th>
                </tr> 
            </thead> 
            <tbody> 
                <?php if (!empty($note)){                                                
                foreach ($note as $key => $value) { ?>
                    <tr>
                        <th scope="row"><?= ++$key; ?></th>
                       
                        <td><?= $value['event_name']; ?></td>
                        <td><?= $value['date_note']; ?></td>
                        <td><?= $value['time_note']; ?></td>
                        <td><?= $value['venue']; ?></td>
                        <td><?= $value['description']; ?></td>                
                    </tr>
                <?php }
            } ?>
            </tbody> 
        </table>
    </div>
</div>



