<?PHP
	/**
	 * drop_non_franchise view.
	 *
	 * confirms that franchise should be run, then runs it on confirmation
	 *
	 */
	//d($this->_ci_cached_vars);
?>
	<script >
		
			$("#confirm_button").on('click',function(){
				var path = '<? echo base_url(); ?>Admin/drop_non_franchise/1';
				
				$("#load_area").load(path);
				
			});
	
	</script>
    <? 
	
	echo '<div class="alert alert-info">'.$message.'</div>';
	//first load, confirm the running of franchise
	if($confirm==0){
		echo '<div class="text-center"><span class="alert alert-warning pointer" id="confirm_button">Confirm</span></div>';
	}

	
	
	?>


<?PHP
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/