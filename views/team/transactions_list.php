<?PHP
	/**
	 * transactions year view.
	 *
	 * through ajax will display the given year's transactions
	 */
	//d($this->_ci_cached_vars);
?>

	<script type="text/javascript">

		function filter(){
			//show them all first
			$('.transactions_filter').show();
			//check each button. if danger, hide the class
			if($('#PUP_filter_btn').hasClass("fade"))	{
				$('.PUP_filter').hide();
			}
			if($('#PS_filter_btn').hasClass("fade"))	{
				$('.PS_filter').hide();
			}
			if($('#FA_filter_btn').hasClass("fade"))	{
				$('.FA_filter').hide();
			}
			if($('#Release_filter_btn').hasClass("fade"))	{
				$('.Release_filter').hide();
			}
			if($('#Trade_filter_btn').hasClass("fade"))	{
				$('.Trade_filter').hide();
			}

		}
		
		$('.filter_button').on("click", function() {
			$(this).toggleClass("fade");
		
			filter();
		});

			
	</script>
    <div class="row"> 
        <div id="year_selector" class="col-xs-24 page_title hidden-sm hidden-md hidden-lg">
            <small>Select a Year:</small><br>			
            <button class="btn btn-default dropdown-toggle" type="button" id="year_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                <span id="dropdown_year"><? echo $year; ?></span>
                <span class="caret"></span>
            </button>
            
            <ul class="dropdown-menu"  aria-labelledby="dropdownMenu1" >
                <?php
                $select_year = $current_year;
                
                while($select_year>=$first_year)
                {
                    ?><li><a href="#" onClick="change_content('<? echo base_url()."Team/transaction/".$team_id."/".$select_year; ?>',$('#dropdown_title').html())" ><? echo $select_year; ?></a></li>
                
                <?	$select_year--; 
                } ?>
            </ul>
               
        </div>
    
    	<div class="col-xs-24 col-sm-18">
    	
            <button type="button" class="btn btn-primary btn-sm filter_button" id="PUP_filter_btn" >
                     PUP 
            </button>
            <button type="button" class="btn btn-primary btn-sm filter_button" id="PS_filter_btn" >
                     PS 
            </button>
            <button type="button" class="btn btn-primary btn-sm filter_button" id="FA_filter_btn" >
                     FA 
            </button>
            <button type="button" class="btn btn-primary btn-sm filter_button" id="Release_filter_btn" >
                     Release 
            </button>
            <button type="button" class="btn btn-primary btn-sm filter_button" id="Trade_filter_btn" >
                     Trade 
            </button>

        </div>
    </div>
    <div class="row">
    	<div class="col-xs-24 col-sm-18">
            <div class="panel panel-primary blue_panel-primary" style="margin-top:5px;" id="transactions_panel">
                <div class="panel-heading blue_panel-heading">
                    <h3 class="panel-title blue_panel-title">
                       <strong><? echo $year; ?> Transactions</strong>
                    </h3>
                </div> 
                <div class="panel-body ">
                    <table class="table table-condensed table-striped table-responsive table-hover">
                        <thead>
                            <th class="text-center col-xs-8 col-sm-6" style="border-bottom:solid #ccc 1px; "><small>Team</small></th>
                            
                            <th class="text-center col-xs-2 col-sm-2" style="border-bottom:solid #ccc 1px; "><small>Type</small></th>
                            <th class="text-center col-xs-11 col-sm-12" style="border-bottom:solid #ccc 1px; " ><small>Transaction</small></th>
                            <th class="text-center col-xs-3 col-sm-4" style="border-bottom:solid #ccc 1px; "><small>Time</small></th>
                        </thead>
                        <tbody>
    
                    
                    
                    <?
                
                    if($transactions_array){
    
                         foreach($transactions_array as $transaction){ 
						
                            //add filter classes
                            $type = explode(' ',$transaction['transaction_type']);
                            if(count($type)>1) { $type = $type['1']; } else { $type = $type['0']; }
                            $filter_class=$type.'_filter'; 
                            if($transaction['team_id']==$team_id){ $team_filter =''; } else { $team_filter='team_filter'; }
                            
                            //tr for filtering
                            echo '<tr class=" transactions_filter '.$filter_class.' '.$team_filter.'" style="border-bottom:solid #ccc 1px; padding-left:0px; padding-right:0px;">';
    
                                echo '<td class="text-left col-xs-8 col-sm-6"><small>'.team_name_link($transaction['team_id']).'</small></td>';
                                echo '<td class="text-center col-xs-2 col-sm-2 "><small>'.$type.'</small></td>';
                                echo '<td class="text-center col-xs-11 col-sm-12 "><small>'.$transaction['text'].'</small></td>';
                                echo '<td class="text-center col-xs-3 col-sm-4"  ><small>'.date('M j g:ia',$transaction['time']).'</small></td>';
    
                            echo '</tr>';
                            
                        }
                    } ?>
                    </tbody>
                </table>
            </div>
		</div>
    </div>
    <div id="year_selector" class="col-sm-6 pull-right page_title hidden-xs">
			<small>Select a Year:</small><br>			
            <button class="btn btn-default dropdown-toggle" type="button" id="year_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                <span id="dropdown_year"><? echo $year; ?></span>
                <span class="caret"></span>
            </button>
            
            <ul class="dropdown-menu"  aria-labelledby="dropdownMenu1" >
                <?php
				$select_year = $current_year;
				
                while($select_year>=$first_year)
                {
                    ?><li><a href="#" onClick="change_content('<? echo base_url()."Team/transaction/".$team_id."/".$select_year; ?>')" ><? echo $select_year; ?></a></li>
                
				<?	$select_year--; 
				} ?>
            </ul>
       </div>
    </div>

       
 


<?PHP
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/