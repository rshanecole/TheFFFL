<?PHP
	/**
	 * admin_panel view.
	 *
	 * 
	 */
	d($this->_ci_cached_vars);
?>
	<script >
		
		
		var modal = "<!--admin modal -->\
			<div class='modal fade' id='admin_modal' tabindex='-1' role='dialog' aria-labelledby='admin_modal_Label'>\
				<div class='modal-dialog' role='document'>\
					<div class='modal-content'>\
						<div class='modal-header'>\
							<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>\
							<h4 class='modal-title' style='text-transform:capitalize;' id='admin_modal_Label'>Admin Action</h4>\
						</div>\
						<div class='modal-body' id='load_area'>\
						</div>\
						<div class='modal-footer'>\
							<div type='button' class='btn btn-primary' id='save'>Save Changes</div>\
						</div>\
					</div>\
				</div>\
			</div>";
		jQuery(function() { $('#modal_area').html(modal); });
		
		$(document).on('click', '.action_link', function(){
			var id = $(this).attr('id');
			var path ='<? echo base_url(); ?>Admin/' + id;
			$('#admin_modal_Label').html(id.replace(/_/g,' '));
			$('#load_area').load(path);
				
		});
		
		
		
		<? if ($_SESSION['security_level']<3){ ?>
			jQuery(function() { $('.level_3').hide(); });
		<? } ?>
		
		
	</script>
    <div id="content_area" class="">
        <div class="col-xs-24 col-sm-12 col-md-8">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="panel-title">Players</div>
                </div>
                <div class="panel-body">
                    <div class=" ">
                    	<a href="#" class="action_link" data-toggle="modal" data-target="#admin_modal" id="adjust_team_player">Adjust a player for a specific team</a>
                    </div>
                </div>
            </div>
        </div>
        
   
        <div class="col-xs-24 col-sm-12 col-md-8">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="panel-title">League</div>
                </div>
                <div class="panel-body">
                    <div class="level_3 ">
                    	<a href="#" class="action_link" data-toggle="modal" data-target="#admin_modal" id="fb_token/1">Update Facebook Token</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xs-24 col-sm-12 col-md-8">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="panel-title">Season</div>
                </div>
                <div class="panel-body">
                    <div class="level_3 ">
                    	<a href="#" class="action_link" data-toggle="modal" data-target="#admin_modal" id="new_week/1">Finalize Week</a>
                    </div>
                    <div class="level_3 ">
                    	<a href="#" class="action_link" data-toggle="modal" data-target="#admin_modal" id="set_gow">Set GOW</a>
                	</div>
                </div>
                
            </div>
        </div>
    
        <div class="col-xs-24 col-sm-12 col-md-8">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="panel-title">Preseason</div>
                </div>
                
                <div class="panel-body">
                	<!--Drop non franchise -->
                    <div class="level_3 ">
                    	<a href="#" class="action_link" data-toggle="modal" data-target="#admin_modal" id="drop_non_franchise">Drop Non-Franchise</a>
                    </div>
                	<!--import projections -->
                    <div class="level_3 ">
                    	<a href="#" class="action_link" data-toggle="modal" data-target="#admin_modal" id="import_projections">Update Projections</a>
                    </div>
                    <!-- view the projection rankings -->
                     <div class="level_3 ">
                    	<a href="<? echo base_url().'Admin/load_projections/'.$year; ?>" id="load_projections">View Projections</a>
                    </div>
                     <!-- view the projection rankings -->
                     <div class="level_3 ">
                    	<a href="#" class="action_link" data-toggle="modal" data-target="#admin_modal" id="preseason_predictions">Preseason Predictions</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xs-24 col-sm-12 col-md-8">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="panel-title">Drafts</div>
                </div>
                <div class="panel-body">
                    <div class="level_3 ">
                    	<a href="#" class="action_link" data-toggle="modal" data-target="#admin_modal" id="swap_teams_draft_day">Swap Team Draft Day</a>
                    </div>
                    <div class="level_3 ">
                    	<a href="#" class="action_link" data-toggle="modal" data-target="#admin_modal" id="adjust_fa_draft_order">Adjust FA Draft Order</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="content_area" class="">
        <div class="col-xs-24 col-sm-12 col-md-8">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="panel-title">Owners</div>
                </div>
                <div class="panel-body">
                    <div class="level_3 ">
                    	<a href="#" class="action_link" data-toggle="modal" data-target="#admin_modal" id="adjust_owner_profile">Adjust owner profile</a>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
   
   </div><!-- extra /div needed for some reason -->
   
	</div>


<?PHP
/*End of file franchise.php*/
/*Location: ./application/veiws/Team/franchise.php*/