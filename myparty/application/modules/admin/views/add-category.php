

<div class="forms">
	<h2 class="title1"><?= $title; ?></h2>
	<?= $this->session->flashdata('msg'); ?>
	<div class="row">		
		<div class="form-three widget-shadow">
			<form class="form-horizontal" method="post" action="<?php if(!empty($category)){ echo site_url('admin/editcategory');}else{
        	echo  base_url('admin/addcategory');
        } ?>" enctype="multipart/form-data" >
				<h3 class="text-center">Category Details</h3><br>
			
				<div class="form-group">
					<label for="focusedinput" class="col-sm-2 control-label">Name</label>
					<div class="col-sm-8">
						<input type="text" name="category_name" class="form-control" placeholder="category Name"  <?php if(!empty($category)) echo 'value="'.$category['category_name'].'"'; ?>>

						   <p><?php echo form_error('category_name', '<span class="error_msg">', '</span>'); ?></p>
					</div>					
				</div>

				<div class="form-group">
					<label class="col-sm-2 control-label label-input-lg">Category Image</label>
					<div class="col-sm-8">
						<input type="file" name="image" >


						 <?php if(!empty($category)){ ?>
                     
                    
                    
                    <br/><br/>
                 <img class="img-responsive" src="<?php echo base_url('/assets/category/'.$category['category_image']); ?>" height="250px" width="200px">
                    <?php }?>



					</div>
				</div>

								
				
				
			
				
			
				<div class="col-sm-offset-2">

					<?php if(!empty($category)){ ?>
  <input type="hidden" name="id" value="<?php echo $category['category_id']; ?>">
                <input type="submit" name="submit" value="Edit" class="btn btn-success">
                <?php } else { ?>
                <input type="submit" name="submit" value="Add" class="btn btn-success">
                <?php } ?>


					
				</div>
			</form>
		</div>
	</div>
</div>	



