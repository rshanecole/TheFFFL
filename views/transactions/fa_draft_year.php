<?PHP
	/**
	 * scoreboard view.
	 *
	 * through ajax will display scores by week	 */
	 
	d($this->_ci_cached_vars);
	//$this->output->enable_profiler(TRUE);
?>
	<script type="text/javascript">
		
			
	</script>
    <style>
		.link_color{
			color:white;
		}
	</style>
    <div style="" id="open_stats"></div>
    <div style="" id="open_scores"></div>
		<div id="week_selector" class="col-xs-24 page_title text-center " style="padding-bottom:5px;">
        	<div class="btn-group">
            <button class="btn btn-default dropdown-toggle" type="button" id="group_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                <span id="dropdown_group">Week <? echo $week; ?></span>
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu"  aria-labelledby="dropdownMenu1" >
                <?
                $w=1;
                while($w<=17){ ?>
                    <li><a href="#" onClick="change_content('<? echo base_url()."Free_Agent/fa_draft/".$year."/".$w."/".$day; ?>',$('#dropdown_title').html())" ><? echo $w; ?></a></li>
                    
                <? $w++; 
                } ?>
            </ul>
            </div>
   	 	</div><!--content_selector-->
    	<div id="games" class="col-xs-24 col-sm-24 col-md-17" >
        
		</div>
        <div id="nfl_games" class="hidden-xs hidden-sm col-md-7" >
        
		</div>
        
       

       


<?PHP
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/