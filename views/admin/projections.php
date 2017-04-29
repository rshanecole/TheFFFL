<?PHP
	/**
	 * adjust_team_player view.
	 *
	 * loads the admin view for adjusting a player for a specific team
	 *
	 */
	d($this->_ci_cached_vars);
?>
	
		<script type="text/javascript" src="<? echo base_url(); ?>assets/js/jquery.tablesorter.min.js"></script>

	<script >
		function ReloadPage() { 
		   location.reload();
		};
		
	
		  
		$(function(){
			$('#all_vbd').tablesorter(); 
			  $('#qb_vbd').tablesorter(); 
			  $('#rb_vbd').tablesorter(); 
			  $('#wr_vbd').tablesorter(); 
			  $('#te_vbd').tablesorter(); 
			  $('#k_vbd').tablesorter(); 
			  $('.LstSt').trigger('click');
			  $('.LstSt').trigger('click');
			  setTimeout(function() {
				   $("#qb_body tr:gt(19)").hide();
			 
			  $("#rb_body tr:gt(19)").hide();
			  
			  $("#wr_body tr:gt(29)").hide();
			
			  $("#te_body tr:gt(14)").hide();
			  
			  $("#k_body tr:gt(30)").hide();
				  
			  },2000);
		  
		  setTimeout("ReloadPage()", 100000); 
		})
		
		
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
        <div class="col-xs-24 col-sm-12 ">
            <div class="panel panel-primary">
                <div class="panel-heading text-center">
                    <div class="panel-title">All Players VBD</div>
                </div>
                <div class="panel-body">
                	 
                    <table class="table table-condensed numbered_table" id="all_vbd">
                    	<thead>
                        	<tr>
                            	<th><small>Player</small></th>
                                <th><small>St 1</small></th>
                                <th class="LstSt"><small>Lst St</small></th>
                                <th><small>BU 1</small></th>
                                <th><small>Lst BU</small></th>
                                <th><small>ADP</small></th>
                                <th><small>SD</small></th>
                            </tr>
                        </thead>
                        <tbody>
                        	<? 
							
							foreach($all_players as $fffl_player_id=>$data){
								$number='';
								if($data['available']==0) { $highlight = "bg-danger"; }
								elseif($data['available']==1) { $highlight = "bg-warning"; }
								elseif($data['available']==2) { $highlight = "bg-info"; $number = "numbered";}
								if(in_array($fffl_player_id,$team_roster)){ $highlight = "bg-success"; }
								echo '<tr class="'.$number.' '.$highlight.'">';
								
									echo '<td class="col1" ><small>';
										echo player_name_link($fffl_player_id,TRUE,TRUE);
									echo '</small></td>';
									echo '<td class="vbd col2 text-center"><small>';
										echo $data['starters1'];
									echo '</small></td>';
									echo '<td class="vbd col3 text-center" ><small>';
										echo $data['last_starter'];
									echo '</small></td>';
									echo '<td class="vbd col4 text-center" ><small>';
										echo $data['first_backup'];
									echo '</small></td>';
									echo '<td class="vbd col5 text-center" ><small>';
										echo $data['last_backup'];
									echo '</small></td>';
									echo '<td class="col6 text-center"><small>';
										if($data['adp']==999) { echo '<span style="opacity: 0.0;">'.$data['adp'].'</span>' ; } else { echo $data['adp']; }
									echo '</small></td>';
									echo '<td class="col7 text-center"><small>';
										if($data['standard_deviation']) { echo $data['standard_deviation']; }
									echo '</small></td>';
								echo '</tr>';
							} ?>
                        </tbody>
                     </table>
                    </div>
                   
                </div>
            </div>
        </div>
        
   		<!-- QB -->
        
        <div class="col-xs-24 col-sm-12 ">
            <div class="panel panel-primary">
                <div class="panel-heading text-center">
                    <div class="panel-title">QB</div>
                </div>
                <div class="panel-body">
                	 
                    <table class="table table-condensed numbered_table" id="qb_vbd">
                    	<thead>
                        	<tr>
                            	<th><small>Player</small></th>
                                <th><small>St 1</small></th>
                                <th class="LstSt"><small>Lst St</small></th>
                                <th><small>BU 1</small></th>
                                <th><small>Lst BU</small></th>
                                <th><small>ADP</small></th>
                            </tr>
                        </thead>
                        <tbody id="qb_body">
                        	<? 
							$count=0;
							foreach($all_players as $fffl_player_id=>$data){
								
								if($data['position']!='QB') { continue; }
								if($data['available']==0) { $highlight = "bg-danger"; }
								elseif($data['available']==1) { $highlight = "bg-warning"; }
								elseif($data['available']==2) { $highlight = "bg-info"; }
								if(in_array($fffl_player_id,$team_roster)){ $highlight = "bg-success"; }
								
								echo '<tr class="'.$highlight.' numbered">';
								
									echo '<td class="col1" ><small>';
										echo player_name_link($fffl_player_id,TRUE,TRUE);
									echo '</small></td>';
									echo '<td class="vbd col2 text-center"><small>';
										echo $data['starters1'];
									echo '</small></td>';
									echo '<td class="vbd col3 text-center" ><small>';
										echo $data['last_starter'];
									echo '</small></td>';
									echo '<td class="vbd col4 text-center" ><small>';
										echo $data['first_backup'];
									echo '</small></td>';
									echo '<td class="vbd col5 text-center" ><small>';
										echo $data['last_backup'];
									echo '</small></td>';
									echo '<td class="col6 text-center"><small>';
										if($data['adp']==999) { echo '<span style="opacity: 0.0;">'.$data['adp'].'</span>' ; } else { echo $data['adp']; }
									echo '</small></td>';
									echo '<td class="col7 text-center"><small>';
										if($data['standard_deviation']) { echo $data['standard_deviation']; }
									echo '</small></td>';
								echo '</tr>';
							} ?>
                            
                        </tbody>
                     </table>
                     <div class="text-center pointer"><span onClick="add_more('#qb_vbd');">More</span> <span onClick="add_less('#qb_vbd');">Less</span></div>
                    </div>
                   
                </div>
            </div>
    
        
        <!-- RB -->
        
        <div class="col-xs-24 col-sm-12 ">
            <div class="panel panel-primary">
                <div class="panel-heading text-center">
                    <div class="panel-title">RB</div>
                </div>
                <div class="panel-body">
                	 
                    <table class="table table-condensed numbered_table" id="rb_vbd">
                    	<thead>
                        	<tr>
                            	<th><small>Player</small></th>
                                <th><small>St 1</small></th>
                                <th class="LstSt"><small>Lst St</small></th>
                                <th><small>BU 1</small></th>
                                <th><small>Lst BU</small></th>
                                <th><small>ADP</small></th>
                            </tr>
                        </thead>
                        <tbody id="rb_body">
                        	<? 
							
							foreach($all_players as $fffl_player_id=>$data){
								if($data['position']!='RB') { continue; }
								if($data['available']==0) { $highlight = "bg-danger"; }
								elseif($data['available']==1) { $highlight = "bg-warning"; }
								elseif($data['available']==2) { $highlight = "bg-info"; }
								if(in_array($fffl_player_id,$team_roster)){ $highlight = "bg-success"; }
								echo '<tr class="'.$highlight.' numbered">';
								
									echo '<td class="col1" ><small>';
										echo player_name_link($fffl_player_id,TRUE,TRUE);
									echo '</small></td>';
									echo '<td class="vbd col2 text-center"><small>';
										echo $data['starters1'];
									echo '</small></td>';
									echo '<td class="vbd col3 text-center" ><small>';
										echo $data['last_starter'];
									echo '</small></td>';
									echo '<td class="vbd col4 text-center" ><small>';
										echo $data['first_backup'];
									echo '</small></td>';
									echo '<td class="vbd col5 text-center" ><small>';
										echo $data['last_backup'];
									echo '</small></td>';
									echo '<td class="col6 text-center"><small>';
										if($data['adp']==999) { echo '<span style="opacity: 0.0;">'.$data['adp'].'</span>' ; } else { echo $data['adp']; }
									echo '</small></td>';
									echo '<td class="col7 text-center"><small>';
										if($data['standard_deviation']) { echo $data['standard_deviation']; }
									echo '</small></td>';
								echo '</tr>';
							} ?>
                            
                        </tbody>
                     </table>
                     <div  class="text-center pointer"><span onClick="add_more('#rb_vbd');">More</span> <span onClick="add_less('#rb_vbd');">Less</span></div>
                    </div>
                   
                </div>
            </div>

        <!-- WR -->
        
        <div class="col-xs-24 col-sm-12 ">
            <div class="panel panel-primary">
                <div class="panel-heading text-center">
                    <div class="panel-title">WR</div>
                </div>
                <div class="panel-body">
                	 
                    <table class="table table-condensed numbered_table" id="wr_vbd">
                    	<thead>
                        	<tr>
                            	<th><small>Player</small></th>
                                <th><small>St 1</small></th>
                                <th class="LstSt"><small>Lst St</small></th>
                                <th><small>BU 1</small></th>
                                <th><small>Lst BU</small></th>
                                <th><small>ADP</small></th>
                            </tr>
                        </thead>
                        <tbody id="wr_body">
                        	<? 
							
							foreach($all_players as $fffl_player_id=>$data){
								if($data['position']!='WR') { continue; }
								if($data['available']==0) { $highlight = "bg-danger"; }
								elseif($data['available']==1) { $highlight = "bg-warning"; }
								elseif($data['available']==2) { $highlight = "bg-info"; }
								if(in_array($fffl_player_id,$team_roster)){ $highlight = "bg-success"; }
								echo '<tr class="'.$highlight.' numbered">';
								
									echo '<td class="col1" ><small>';
										echo player_name_link($fffl_player_id,TRUE,TRUE);
									echo '</small></td>';
									echo '<td class="vbd col2 text-center"><small>';
										echo $data['starters1'];
									echo '</small></td>';
									echo '<td class="vbd col3 text-center" ><small>';
										echo $data['last_starter'];
									echo '</small></td>';
									echo '<td class="vbd col4 text-center" ><small>';
										echo $data['first_backup'];
									echo '</small></td>';
									echo '<td class="vbd col5 text-center" ><small>';
										echo $data['last_backup'];
									echo '</small></td>';
									echo '<td class="col6 text-center"><small>';
										if($data['adp']==999) { echo '<span style="opacity: 0.0;">'.$data['adp'].'</span>' ; } else { echo $data['adp']; }
									echo '</small></td>';
									echo '<td class="col7 text-center"><small>';
										if($data['standard_deviation']) { echo $data['standard_deviation']; }
									echo '</small></td>';
								echo '</tr>';
							} ?>
                            
                        </tbody>
                     </table>
                     <div class="text-center pointer"><span onClick="add_more('#wr_vbd');">More</span> <span onClick="add_less('#wr_vbd');">Less</span></div>
                    </div>
                   
                </div>
            </div>
            
        <!-- TE -->
        
        <div class="col-xs-24 col-sm-12 ">
            <div class="panel panel-primary">
                <div class="panel-heading text-center">
                    <div class="panel-title">TE</div>
                </div>
                <div class="panel-body">
                	 
                    <table class="table table-condensed numbered_table" id="te_vbd">
                    	<thead>
                        	<tr>
                            	<th><small>Player</small></th>
                                <th><small>St 1</small></th>
                                <th class="LstSt"><small>Lst St</small></th>
                                <th><small>BU 1</small></th>
                                <th><small>Lst BU</small></th>
                                <th><small>ADP</small></th>
                            </tr>
                        </thead>
                        <tbody id="te_body">
                        	<? 
							
							foreach($all_players as $fffl_player_id=>$data){
								if($data['position']!='TE') { continue; }
								if($data['available']==0) { $highlight = "bg-danger"; }
								elseif($data['available']==1) { $highlight = "bg-warning"; }
								elseif($data['available']==2) { $highlight = "bg-info"; }
								if(in_array($fffl_player_id,$team_roster)){ $highlight = "bg-success"; }
								echo '<tr class="'.$highlight.' numbered">';
								
									echo '<td class="col1" ><small>';
										echo player_name_link($fffl_player_id,TRUE,TRUE);
									echo '</small></td>';
									echo '<td class="vbd col2 text-center"><small>';
										echo $data['starters1'];
									echo '</small></td>';
									echo '<td class="vbd col3 text-center" ><small>';
										echo $data['last_starter'];
									echo '</small></td>';
									echo '<td class="vbd col4 text-center" ><small>';
										echo $data['first_backup'];
									echo '</small></td>';
									echo '<td class="vbd col5 text-center" ><small>';
										echo $data['last_backup'];
									echo '</small></td>';
									echo '<td class="col6 text-center"><small>';
										if($data['adp']==999) { echo '<span style="opacity: 0.0;">'.$data['adp'].'</span>' ; } else { echo $data['adp']; }
									echo '</small></td>';
									echo '<td class="col7 text-center"><small>';
										if($data['standard_deviation']) { echo $data['standard_deviation']; }
									echo '</small></td>';
								echo '</tr>';
							} ?>
                            
                        </tbody>
                     </table>
                     <div class="text-center pointer"><span onClick="add_more('#te_vbd');">More</span> <span onClick="add_less('#te_vbd');">Less</span></div>
                    </div>
                   
                </div>
            </div>

        <!-- K -->
        
        <div class="col-xs-24 col-sm-12 ">
            <div class="panel panel-primary">
                <div class="panel-heading text-center">
                    <div class="panel-title">K</div>
                </div>
                <div class="panel-body">
                	 
                    <table class="table table-condensed numbered_table" id="k_vbd">
                    	<thead>
                        	<tr>
                            	<th><small>Player</small></th>
                                <th><small>St 1</small></th>
                                <th class="LstSt"><small>Lst St</small></th>
                                <th><small>BU 1</small></th>
                                <th><small>Lst BU</small></th>
                                <th><small>ADP</small></th>
                            </tr>
                        </thead>
                        <tbody id="k_body">
                        	<? 
							
							foreach($all_players as $fffl_player_id=>$data){
								if($data['position']!='K') { continue; }
								if($data['available']==0) { $highlight = "bg-danger"; }
								elseif($data['available']==1) { $highlight = "bg-warning"; }
								elseif($data['available']==2) { $highlight = "bg-info"; }
								if(in_array($fffl_player_id,$team_roster)){ $highlight = "bg-success"; }
								echo '<tr class="'.$highlight.' numbered">';
								
									echo '<td class="col1" ><small>';
										echo player_name_link($fffl_player_id,TRUE,TRUE);
									echo '</small></td>';
									echo '<td class="vbd col2 text-center"><small>';
										echo $data['starters1'];
									echo '</small></td>';
									echo '<td class="vbd col3 text-center" ><small>';
										echo $data['last_starter'];
									echo '</small></td>';
									echo '<td class="vbd col4 text-center" ><small>';
										echo $data['first_backup'];
									echo '</small></td>';
									echo '<td class="vbd col5 text-center" ><small>';
										echo $data['last_backup'];
									echo '</small></td>';
									echo '<td class="col6 text-center"><small>';
										if($data['adp']==999) { echo '<span style="opacity: 0.0;">'.$data['adp'].'</span>' ; } else { echo $data['adp']; }
									echo '</small></td>';
									echo '<td class="col7 text-center"><small>';
										if($data['standard_deviation']) { echo $data['standard_deviation']; }
									echo '</small></td>';
								echo '</tr>';
							} ?>
                            <tr >
                        </tbody>
                        <div class="text-center pointer"><span onClick="add_more('#k_vbd');">More</span> <span onClick="add_less('#k_vbd');">Less</span></div>
                     </table>
                    </div>
                   
                </div>
            </div>
   
 	</div>  
    </div>


<?PHP
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/