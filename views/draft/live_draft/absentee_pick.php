<?PHP
	/**
	 * absentee pick view.
	 *
	 * allows a pick for another team to be recorded
	 */
d($this->_ci_cached_vars);
?>

   <script>
   	$(document).ready(function(){
   		$(".add_player").on("click",function(){
				var fffl_player_id = $(this).attr('data-player_id');
				$.ajax({
						url: "<? echo base_url(); ?>Draft_Live/add_selection/" + fffl_player_id + "/<? echo $draft_id.'/'.$team_id; ?>",
						type: 'POST',
						success: function() {
							location.reload();
						}
					});	
			
		});
		
		$(".remove_player").on("click",function(){
				var fffl_player_id = $(this).attr('id');
				$.ajax({
						url: "<? echo base_url(); ?>Draft_Live/remove_selection/" + fffl_player_id + "/<? echo $draft_id.'/'.$team_id; ?>",
						type: 'POST',
						success: function() {
							location.reload();
						}
					});	
			
		});
		
		$("#autodraft").on("click",function(){
				var autodraft = $(this).attr('data-autodraft_setting');
				if(autodraft>0) { var new_autodraft =0; } else { var new_autodraft=1; }
				$.ajax({ 
						url: "<? echo base_url(); ?>Draft_Live/update_autodraft/<? echo $team_id; ?>/<? echo $draft_id; ?>/"+new_autodraft,
						type: 'POST',
						success: function() {
							location.reload();
						}
					});	
			
		});
	});
   
   </script>
        
        <div id="content_area" class="">
        	<div class="row">
                <div CLASS="col-xs-24 text-center" id="autodraft" data-autodraft_setting="<? echo $autodraft; ?>">Autodraft: <? if($autodraft==0){ echo 'off'; } else { echo 'on'; } ?><br><br></div>
                <div id="" class="col-xs-24 text-center" style="margin-top:10px;">
                    
					<? foreach($team_draft_list as $fffl_player_id){
						
						echo '<div id='.$fffl_player_id.' class="remove_player">'.player_name_no_link($fffl_player_id).'</div>';	
						
					}
				?>
                </div>
                
                <div class="col-xs-24 text-center"><br><br>Available Players<br><br></div>
                <div id="" class="col-xs-24 text-center" style="margin-top:10px;">
                    
					<? foreach($draftable as $fffl_player_id=>$position){
						
						echo '<div data-player_id="'.$fffl_player_id.'" class="add_player">'.player_name_no_link($fffl_player_id).'</div>';	
						
					}
				?>
                </div>
            </div>
			
        </div> <!-- end content_area div -->
	</div>


<?PHP 
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/