<?PHP
	/**
	 * adjust_team_player view.
	 *
	 * loads the admin view for adjusting a player for a specific team
	 *
	 */
	//d($this->_ci_cached_vars);
?>
	<script >
		
			$('select[name="teams"]').change(function(e) {
				e.preventDefault();
				var team_id = $('select[name="teams"]').val();
				var path = '<? echo base_url(); ?>Admin/adjust_team_player/'+team_id;
				$('#status2').append('teams--');
				$("#load_area").load(path);
			});
			
			$('select[name="players"]').change(function(e) {
				e.preventDefault();
				var fffl_player_id = $('select[name="players"]').val();
				if(fffl_player_id > 0){
					var path = '<? echo base_url(); ?>Admin/adjust_team_player/'+$('select[name="teams"]').val()+'/'+fffl_player_id;
					$('#status2').append('players--');
					$("#load_area").load(path);
				}
             
			});
			
			$('#save').off('click').on('click',function(e) {
				e.preventDefault();
              //	if(e.handled !==true){//this is here because loading the .php 3 times to get to having a team and player has loaded the jquery 3 times and fires the on function 3 times. potentially it's from the bootstrap actually loading its modal but I doubt that
					$('#status2').append('click--');
					var url_path = '<? echo base_url(); ?>Admin/update_team_player_data/';
					
					$.ajax({
						type: "POST",
						url: url_path,
						data: {
							fffl_player_id: $('select[name="players"]').val(),
							team_id: $('select[name="teams"]').val(),
							salary: $('#salary').val(),
							area: $('select[name="area"]').val(),
							weeks_on_pup: $('#weeks_on_pup').val()
							
							},
						success: function(data) {
						var obj = jQuery.parseJSON(data);
							$('#status2').append(obj['player']+' '+obj['salary']+'--');
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
    
     <div class="text-center " style="margin-bottom:3px;">
		<?php 
		$all_teams = array("0"=>'Choose a Team') + $all_teams;
		echo form_dropdown('teams', $all_teams,$team_id);
		?>
     </div>
     <?  
	 if($team_id){ ?>
        <div class="text-center "  style="margin-bottom:3px;">
            <?php 
            $all_players = array("0"=>'Choose a Player') + $all_players;
            echo form_dropdown('players', $all_players, $fffl_player_id);
            ?>
         </div>
     <? } 
     
	 //a player is selected. Now display his info in a form
     if($team_id && $fffl_player_id){ ?>
		
			<div class="col-xs-24 ">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<div class="panel-title text-center"><? echo $all_players[$fffl_player_id].' '.$team_id; ?></div>
					</div>
					<div class="panel-body">
						<div class="col-xs-6">
                        	Area:
                        </div>
                        <div class="col-xs-6">
                        	
						<? 
							echo form_dropdown('area',array("Roster"=>"Roster","PUP"=>"PUP","PS"=>"PS"),$player_info['area']);
						?>	
						</div>
                        <div class="col-xs-12">Changing player's area will not record transaction. To make a transaction, do so from team page.</div>
                        <div class="col-xs-6">
                        	Weeks on PUP:
                        </div>
                        <div class="col-xs-18">
						<? 
							$input_attributes = array(
								'name' => 'weeks_on_pup',
								'id' => 'weeks_on_pup',
								'value' => $player_info['weeks_on_pup'],
								'maxlength' => '2',
								'size' => '5'
							);
							echo form_input($input_attributes);
						?>	
						</div>
                        <div class="col-xs-6">
                        	Salary:
                        </div>
                        <div class="col-xs-18">
						<? 
							$input_attributes = array(
								'name' => 'salary',
								'id' => 'salary',
								'value' => $player_info['salary'],
								'maxlength' => '2',
								'size' => '5'
							);
							echo form_input($input_attributes);
						?>	
						</div>
					</div>
				</div>
			</div>
			
		<? 
	 } 
	 unset($fffl_player_id,$team_id);
	 ?>


<?PHP
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/