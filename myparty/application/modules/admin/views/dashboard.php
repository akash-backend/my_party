
<div class="col_3">
	
	<div class="col-md-3 widget widget1">
		<div class="r3_counter_box">
            <i class="pull-left fa fa-user user1 icon-rounded"></i>
            <div class="stats">
             <h5><strong><?php echo $all_user?></strong></h5>
              <span>Total Users</span>
            </div>
        </div>
	</div>

    <div class="col-md-3 widget widget1">
        <div class="r3_counter_box">
            <i class="pull-left fa fa-users dollar1 icon-rounded"></i>
            <div class="stats">
              <h5><strong><?php echo $all_provider?></strong></h5>
              <span>Total Provider</span>
            </div>
        </div>
     </div>

     
 	<div class="col-md-3 widget widget1">
		<div class="r3_counter_box">
            <i class="pull-left fa  fa-check-square user2 icon-rounded"></i>
            <div class="stats">
              <h5><strong><?php echo $all_event?></strong></h5>
              <span>Total Event</span>
            </div>
        </div>
	</div>
	
<!--	<div class="col-md-3 widget">
		<div class="r3_counter_box">
            <i class="pull-left fa fa-users dollar2 icon-rounded"></i>
            <div class="stats">
              <h5><strong><?php echo $all_user?></strong></h5>
              <span>Total Users</span>
            </div>
        </div>
	 </div> -->
	<div class="clearfix"> </div>
</div>


<!--pie-chart --><!-- index page sales reviews visitors pie chart -->
<script src="<?= base_url('assets/js/pie-chart.js'); ?>" type="text/javascript"></script>
    <script type="text/javascript">

        $(document).ready(function () {
            $('#demo-pie-1').pieChart({
                barColor: '#2dde98',
                trackColor: '#eee',
                lineCap: 'round',
                lineWidth: 8,
                onStep: function (from, to, percent) {
                    $(this.element).find('.pie-value').text(Math.round(percent) + '%');
                }
            });

            $('#demo-pie-2').pieChart({
                barColor: '#8e43e7',
                trackColor: '#eee',
                lineCap: 'butt',
                lineWidth: 8,
                onStep: function (from, to, percent) {
                    $(this.element).find('.pie-value').text(Math.round(percent) + '%');
                }
            });

            $('#demo-pie-3').pieChart({
                barColor: '#ffc168',
                trackColor: '#eee',
                lineCap: 'square',
                lineWidth: 8,
                onStep: function (from, to, percent) {
                    $(this.element).find('.pie-value').text(Math.round(percent) + '%');
                }
            });

           
        });

    </script>