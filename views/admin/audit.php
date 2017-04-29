<?PHP
	/**
	 * adjust_team_player view.
	 *
	 * loads the admin view for adjusting a player for a specific team
	 *
	 */
	//d($this->_ci_cached_vars);
?>
	
		<script type="text/javascript" src="<? echo base_url(); ?>assets/js/jquery.tablesorter.min.js"></script>

	<script >		
		$(function(){
		
		  $('#all_fa').tablesorter(); 
		  /*$('#qb_vbd').tablesorter(); 
		  $('#rb_vbd').tablesorter(); 
		  $('#wr_vbd').tablesorter(); 
		  $('#te_vbd').tablesorter(); 
		  $('#k_vbd').tablesorter(); 
		  $('.LstSt').click();
		  $('.LstSt').click();
		  $("#qb_body tr:gt(19)").hide();
		 
		  $("#rb_body tr:gt(19)").hide();
		  
		  $("#wr_body tr:gt(29)").hide();
		
		  $("#te_body tr:gt(14)").hide();
		  
		  $("#k_body tr:gt(14)").hide();*/
		  
		});
		
		function add_more(id){
			var hide_rows = $(id+' tr:visible').length + 9;
			$(id+" tr").show();	
			$(id+" tr:gt(" + hide_rows + ")").hide();
		}
		function add_less(id){
			var hide_rows = $(id+' tr:visible').length - 11;
			$(id+" tr").show();	
			$(id+" tr:gt(" + hide_rows + ")").hide();
		}
	</script>
    
    <div id="content_area" class="">
        <div class="col-xs-24 col-sm-8 ">
            <div class="panel panel-primary">
                <div class="panel-heading text-center">
                    <div class="panel-title">FA as of <? echo date('D M j',$fa_time); ?></div>
                </div>
                <div class="panel-body" style="height:400px;overflow:auto">
                	 
                    <table class="table table-condensed numbered_table table-responsive" id="all_fa"  >
                    	<thead>
                        	<tr>
                            	<th><small>Pos</small></th>
                                <th><small>Name</small></th>
                                <th class="LstSt"><small>Team</small></th>
                                <th><small>Bye</small></th>
                                <th><small>Salary</small></th>
                            </tr>
                        </thead>
                        <tbody>
                        	<?
							foreach($fa_players as $fffl_player_id=>$data){
								echo '<tr class="">';
									echo '<td class="col1" ><small>';
										echo $data['position'];
									echo '</small></td>';
									echo '<td class="col2" ><small>';
										echo player_name_link($fffl_player_id,FALSE,FALSE);
									echo '</small></td>';
									echo '<td class="vbd col3 text-center"><small>';
										echo $data['current_team'];
									echo '</small></td>';
									echo '<td class="vbd col4 text-center" ><small>';
										echo $data['bye'];
									echo '</small></td>';
									echo '<td class="vbd col5 text-center" ><small>';
										echo $data['salary'];
									echo '</small></td>';
								echo '</tr>';
							} ?>
                        </tbody>
                     </table>
                    </div>
                   
                </div>
            </div>
        </div>
        
   		
 	</div>  
    </div>


<?PHP
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/