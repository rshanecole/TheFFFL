<?PHP
	/**
	 * adjust_owner profile view.
	 *
	 * loads the admin view for adjusting a profile
	 */
	//d($this->_ci_cached_vars);
?>
	<script >
		
			$('select[name="owners"]').change(function(e) {
				e.preventDefault();
				var user_id = $('select[name="owners"]').val();
				var path = '<? echo base_url(); ?>Account/update/'+user_id;
				
				var win = window.open(path, '_blank');
					if (win) {
						//Browser has allowed it to be opened
						win.focus();
					} else {
						//Browser has blocked it
						alert('Please allow popups for this website');
					}
			});

	</script>
    <? 
	//first load, need to select owner

	?>
    
     <div class="text-center " style="margin-bottom:3px;">
		<?php 
		$all_owners = array("0"=>'Choose Owner') + $all_owners;
		echo form_dropdown('owners', $all_owners);
		?>
     </div>



<?PHP
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/