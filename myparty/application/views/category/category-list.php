

<div class="tables">
    <h2 class="title1"><?= $title; ?></h2>
    <?= $this->session->flashdata('msg'); ?>
    <div class="bs-example widget-shadow"> 
        
        <table class="table table-bordered" id="example"> 
            <thead> 
                <tr> 
                    <th>#</th>
                    <th>Name</th>
                     
                    <th>Action</th> 
                </tr> 
            </thead> 
            <tbody> 
                <?php if (!empty($category)){                                                
                foreach ($category as $key => $value) { ?>
                    <tr>
                        <th scope="row"><?= ++$key; ?></th>
                        <td><?= $value['category_name']; ?></td>
                       
                        <td>
                             <a title="Edit" href="<?php echo base_url('admin/editCategory').'/'.$value['category_id']; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a>&nbsp;

                             

                                 <?php $status = $value['status']; ?>
                                <?php if ($status == 1) { ?>
                                <a href="<?php echo base_url('admin/change_status') . '/category_id/' . $value['category_id']; ?>/category_tbl/status/0/category_list" class="btn btn-danger">BLOCK</a>
                                <?php } elseif ($status == 0) { ?>
                                <a href="<?php echo base_url('admin/change_status').'/category_id/'.$value['category_id']; ?>/category_tbl/status/1/category_list" class="btn btn-success">UNBLOCK</a>
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


