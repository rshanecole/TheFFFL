<?PHP
	/**
	 * team view.
	 *
	 * through ajax will display roster, schedule, drafts, franchise, and team history
	 */
	//d($this->_ci_cached_vars);
	function ordinal($number) {
		$ends = array('th','st','nd','rd','th','th','th','th','th','th');
		if ((($number % 100) >= 11) && (($number%100) <= 13)){
			return $number. 'th';
		}
		else{
			return $number. $ends[$number % 10];
		}
	}
?>

	<script type="text/javascript">

		
		
	</script>

        
        <div id="content_area" class="">
        	<div class="row row-eq-height ">
            
                <div id="logo_container text-center vertical_center" class="col-xs-4 col-sm-4">
                <?
                    $image_properties = array(
                        'src'   => $team_logo_path,
                        'class' => 'img-responsive',
                    );
                    echo img($image_properties);
                ?>
                </div>
                <div class="col-xs-20 col-sm-16 text-center vertical_center" id="info">
                   
                    <div >
                        <strong><? echo $wins_losses['wins'].'-'.$wins_losses['losses']; ?> | Pts: <? echo $points; ?> | PA: <? echo $points_against; ?> </strong>
                    	<div class="btn-group dropdown">
                            <span class="dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                                <span class="caret"></span>
                            </span>
                            <ul class="dropdown-menu pull-right" style="padding:5px" role="menu" aria-labelledby="dropdownMenu1">
                            	<li role="presentation" class="ellipses text-center"><small><strong>Strength of Schedule</strong></small></li>
                                <? 
								$rank=1;
								foreach($sos_array as $team_id=>$pa){ ?>
                                	<li role="presentation" class="ellipses"><small><strong><? echo $rank.'. '; ?><a role="menuitem" tabindex="-1" href="#"><? echo team_name_link($team_id).' '.$pa; ?></a></strong></small></li>
                                   <? $rank++;
								 } ?>
                                
                            </ul>
                        </div>
                    </div>
                    
                    <div style="white-space:nowrap">
                        <strong>
                        <? echo floor((time() - $owner_date_of_birth)/31622400); ?> | <? echo $owner_city.', '.$owner_state; ?> | <? echo $owner_occupation; ?> 
                        </strong>
                    </div>
                    <div style="white-space:nowrap">
                        <strong>
                    	<? echo ordinal($current_year-$team_first_year-$team_non_consecutive_years+1); ?> Season | <? echo '<a href="mailto:'.$owner_email.'">'.$owner_email.'</a>'; ?></strong>
                    </div>
                
                </div>
                <div id="photo_container" class="hidden-xs col-sm-4 vertical_center">
                    <?
                    $image_properties = array(
                        'src'   => $owner_picture_path,
                        'class'=> 'img-responsive',
                    );
                    echo img($image_properties);
                    ?>
                </div>
            </div>    
                <div id="ajax_display_area" class="col-xs-24 text-center" style="margin-top:20px;">
                    
					<? echo img(base_url().'assets/img/loading.gif'); ?>

                </div>
            
			
        </div> <!-- end content_area div -->
	</div>


<?PHP 
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/