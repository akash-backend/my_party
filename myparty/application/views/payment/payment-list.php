

<div class="tables">
    <h2 class="title1"><?= $title; ?></h2>
    <?= $this->session->flashdata('msg'); ?>
    <div class="bs-example widget-shadow"> 
        
        <table class="table table-bordered" id="example"> 
            <thead> 
                <tr> 
                    <th>#</th>
                    <th>Received Email</th>
                    <th>Event name</th>
                    <th>Txn id</th>
                    <th>Payment Gross</th>
                    <th>Currency Code</th>
                    <th>Payer Email</th>
                    <th>Payment Status</th>
                </tr> 
            </thead> 
            <tbody> 
                <?php if (!empty($payment)){                                                
                foreach ($payment as $key => $value) { ?>
                    <tr>
                        <th scope="row"><?= ++$key; ?></th>
                        <td><?= $value['email']; ?></td>
                        <td><?= $value['event_name']; ?></td>
                        <td><?= $value['txn_id']; ?></td>
                        <td><?= $value['payment_gross']; ?></td>
                        <td><?= $value['currency_code']; ?></td>
                        <td><?= $value['payer_email']; ?></td>
                        <td><?= $value['payment_status']; ?></td>
                                              
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

