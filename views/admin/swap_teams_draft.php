<?PHP
	/**
	 * swap_teams_draft view.
	 *
	 * loads the admin view for swapping two team's original drafts
	 *
	 */
	//d($this->_ci_cached_vars);
?>
	<script >
		
			$('select[name="team_a"]').change(function(e) {
				e.preventDefault();
				var team_id_a = $('select[name="team_a"]').val();
				var path = '<? echo base_url(); ?>Admin/swap_teams_draft_day/'+team_id_a;
				$('#status2').append('team_a--');
				$("#load_area").load(path);
			});
			
			$('select[name="team_b"]').change(function(e) {
				e.preventDefault();
				var team_id_b = $('select[name="team_b"]').val();
				if(team_id_b > 0){
					var path = '<? echo base_url(); ?>Admin/swap_teams_draft_day/'+$('select[name="team_a"]').val()+'/'+team_id_b;
					$('#status2').append('team_b--');
					$("#load_area").load(path);
				}
             
			});
			
			$('#save').off('click').on('click',function(e) {
				e.preventDefault();
              //	if(e.handled !==true){//this is here because loading the .php 3 times to get to having a team and player has loaded the jquery 3 times and fires the on function 3 times. potentially it's from the bootstrap actually loading its modal but I doubt that
					$('#status2').append('click--');
					var url_path = '<? echo base_url(); ?>Admin/confirm_swap_teams_draft_day/';
					
					$.ajax({
						type: "POST",
						url: url_path,
						data: {
							team_id_a: $('select[name="team_a"]').val(),
							team_id_b: $('select[name="team_b"]').val(),
							draft_id_a: $('#draft_a').attr('draft'),
							draft_id_b: $('#draft_b').attr('draft')
							},
						success: function(data) {
						var obj = jQuery.parseJSON(data);
						$('#swap_area').html(obj);
						}
						
					});
					e.handled=true;
					return false;
              //}
              
			});
			
		
		
	
	
	</script>
    <? 
	//first load, need to select team and player and send back to controller

	?>
    <div id="swap_area">
     <div class="text-center " style="margin-bottom:3px;">
		<?php 
		$all_teams = array("0"=>'Choose a Team') + $all_teams;
		echo form_dropdown('team_a', $all_teams,$team_id_a);
		?>
     </div>
     <?  
	 if($team_id_a){ ?>
        <div class="text-center "  style="margin-bottom:3px;">
            <?php 
            $all_teams = array("0"=>'Choose a Team') + $all_teams;
            echo form_dropdown('team_b', $all_teams, $team_id_b);
            ?>
         </div>
     <? } 
     
	 //teams are selected. Now display info in a form
     if($team_id_a && $team_id_b){ ?>
		
			<div class="col-xs-24 ">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<div class="panel-title text-center">Swap Drafts</div>
					</div>
					<div class="panel-body">
						<div class="col-xs-12">
                        	<? echo team_name_link($team_id_a); ?>
                       
                        
                        	
						<? 
							foreach($team_a_draft as $start_time => $data){ ?>
								
								<div class="row">
									<div class="col-xs-24" id='draft_a' draft='<? echo $data['0']['draft_id']; ?>'>
										<? echo date('D',$start_time); ?>
									</div>
                                </div>
                                <? foreach($data as $pick){ ?>
                                    <div class="row">
                                        <div class="col-xs-24">
                                            <? echo 'Rd. '.$pick['round'].' #'.$pick['pick_number']; ?>
                                        </div>
                                    </div>
									
								<? }
							 } ?>
							
						</div>
                        <div class="col-xs-12">
                        	<? echo team_name_link($team_id_b); ?>
                       
                        
                        	
						<? 
							foreach($team_b_draft as $start_time => $data){ ?>
								
								<div class="row">
									<div class="col-xs-24" id='draft_b' draft='<? echo $data['0']['draft_id']; ?>'>
										<? echo date('D',$start_time); ?>
									</div>
                                </div>
                                <? foreach($data as $pick){ ?>
                                    <div class="row">
                                        <div class="col-xs-24">
                                            <? echo 'Rd. '.$pick['round'].' #'.$pick['pick_number']; ?>
                                        </div>
                                    </div>
									
								<? }
							 } ?>
							
						</div>
					</div>
				</div>
			</div>
			
		<? 
	 } ?> 
	 </div>
	 


<?PHP
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/