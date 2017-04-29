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