<div style="padding: 20px 0px;">
    <h2 style="text-align: center;">Product List</h2>
   
    <div> 
        
        <table style="text-align: center;"> 
          
                <tr> 
                    
                    <th style="border: 1px solid #ccc; border-collapse: collapse; padding: 10px 20px;">Name</th>
                    <th style="border: 1px solid #ccc; border-collapse: collapse; padding: 10px 20px;">Price</th>
                    <th style="border: 1px solid #ccc; border-collapse: collapse; padding: 10px 20px;">Category</th> 
                    <th style="border: 1px solid #ccc; border-collapse: collapse; padding: 10px 20px;">Description</th> 
                    <th style="border: 1px solid #ccc; border-collapse: collapse; padding: 10px 20px;">Quantity</th>
                   
                </tr> 
          


                <?php if (!empty($product_list)){   

                	
            	
                                                       
                foreach ($product_list as $value) { ?>
                    <tr>
                       
                        <td style="border: 1px solid #ccc; border-collapse: collapse; padding: 10px 20px;"><?= $value->product_name; ?></td>
                        <td style="border: 1px solid #ccc; border-collapse: collapse; padding: 10px 20px;"><?= $value->price; ?></td> 
                        <td style="border: 1px solid #ccc; border-collapse: collapse; padding: 10px 20px;"><?= $value->category_name; ?></td>
                        <td style="border: 1px solid #ccc; border-collapse: collapse; padding: 10px 20px;"><?= $value->description; ?></td>
                        <td style="border: 1px solid #ccc; border-collapse: collapse; padding: 10px 20px;"><?= $value->quantity; ?></td>
                    </tr>
                <?php }
			}
         
         		?>
          
        </table>
    </div>
</div>