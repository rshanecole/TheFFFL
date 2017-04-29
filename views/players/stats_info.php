<?PHP
	/**
	 * player scoring view.
	 *
	 * through ajax will display the player's historical scoring
	 */
	//d($this->_ci_cached_vars);
?>

	<script type="text/javascript">


			
	</script> 
    <div class="row " >
		<div class="col-xs-10">
            		<?php
            			$image_properties = array(
							'src' => 'http://static.nfl.com/static/content/public/static/img/fantasy/transparent/200x200/'.$player['nfl_esbid'].'.png',
							'width' => '50%',
							
            			);
            			echo img($image_properties);
						if($player['current_team'] != 'FA' && $player['current_team'] != 'RET') 
						{
							$image_properties = array(
								'src' => base_url().'assets/img/nfl_team_logos/'.$player['current_team'].'.svg',
								'width' => '50%',
							);
							echo img($image_properties);
            			}
            		?>
        </div>
		<div class="col-xs-14" >
            <small><span class="visible-xs-inline">
                   <?php
                    if($player['current_team'] !== 'FA' && $player['current_team'] !== 'RET') 
                    {
                        $image_properties = array(
                            'src' => base_url().'assets/img/nfl_team_logos/'.$player['current_team'].'.svg',
                            'width' => '15%',
                        );
                        echo img($image_properties).nbs(1);
                    }
                    ?>
                </span><strong><? echo player_name_link($player['fffl_player_id'],FALSE,FALSE) ?></strong>
            <br>
            
                
                <?php echo $player['position'].' | '.$player['current_team'];
                if($player['is_rookie'])
                {
                    echo ' | R';	
                }
                echo ' | Bye: '.$player['bye_week']; ?>
            </small>
            <br>
            <small>
			<? 
            if($player['nfl_injury_game_status']!=''){
                echo '<span class="glyphicon glyphicon-plus" aria-hidden="true" style="color:#b01f24; font-size:small;"></span>';
                echo $player['nfl_injury_game_status'].' | '.$player['injury_text']; 
            } ?>
            </small>
        </div>
     </div>
     <div class="row">
        <div class="col-xs-24 text-center" >
            
    
        </div>
        <div class="col-xs-24 " >
           
         <div class="panel panel-primary blue_panel-primary " >
            <div class="panel-heading blue_panel-heading">
                <h4 class="panel-title blue_panel-title text-center"><small>
					<? echo 'Game Stats vs. '.$stats['opponent']; ?></small></h4>
            </div>
            <div class="panel-body">
            <? // d($stats); ?>
            	<? if($stats['completions']>0 || $stats['incompletions']>0 || $stats['interceptions']>0 || $stats['pass_tds']>0) { ?>
                    <div class="col-xs-24">
                    	<small><strong>Passing:</strong> <? echo $stats['completions'].'/'.($stats['completions']+$stats['incompletions']).' '.$stats['pass_yards'].' yds, '.$stats['pass_tds'].' tds, '.$stats['interceptions'].' int'; ?></small>
                    </div>
                
                <? } ?>
                <? if($stats['rushes']>0 || $stats['rush_yards']>0 || $stats['rush_tds']>0) { ?>
                    <div class="col-xs-24">
                    	<small><strong>Rushing:</strong> <? echo $stats['rushes'].' att '.$stats['rush_yards'].' yds, '.$stats['rush_tds'].' tds'; ?></small>
                    </div>
                
                <? } ?>
                <? if($stats['receptions']>0 || $stats['receiving_yards']>0 || $stats['receiving_tds']>0 ) { ?>
                	<div class="col-xs-24">
                    	<small><strong>Receiving:</strong> <? echo $stats['receptions'].' rec '.$stats['receiving_yards'].' yds, '.$stats['receiving_tds'].' tds'; ?></small>
                    </div>
                <? } ?>
                 <? if($stats['punt_return_tds']>0) { ?>
                	<div class="col-xs-24">
                    	<small><strong>Punt Returns:</strong> <? echo $stats['punt_return_tds'].' tds'; ?></small>
                    </div>
                <? } ?>
                <? if($stats['kick_return_tds']>0) { ?>
                	<div class="col-xs-24">
                    	<small><strong>Kick Returns:</strong> <? echo $stats['kick_return_tds'].' tds'; ?></small>
                    </div>
                <? } ?>
                <? if($stats['xps_made']>0 || $stats['xps_missed']>0 || $stats['fgs_made']>0 || $stats['fgs_missed']>0 ) { ?>
                	<div class="col-xs-24">
                    	<small><strong>Kicking:</strong> <? echo $stats['xps_made'].'/'.($stats['xps_made']+$stats['xps_missed']).' xps, '.$stats['fgs_made'].'/'.($stats['fgs_made']+$stats['fgs_missed']).' fgs'; ?></small>
                    </div>
                <? } ?>
                 <? if($stats['fumbles']>0 || $stats['fumbles_lost']>0) { ?>
                	<div class="col-xs-24">
                    	<small><strong>Fumbles:</strong> <? echo $stats['fumbles_lost'].'/'.$stats['fumbles']; ?></small>
                    </div>
                <? } ?>
			</div>
          </div>
       </div>
    </div>


       
 


<?PHP
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/