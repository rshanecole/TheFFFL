<?PHP
	/**
	 * playoff bracket view.
	 *
	 * through ajax will display bracket by year
	 */
	//d($this->_ci_cached_vars);
	//$this->output->enable_profiler(TRUE);
?>
	<script type="text/javascript">
		
	</script>
       

    	<div id="bracket" class="col-xs-24 col-sm-24 col-md-24" >
        	<div class="panel panel-primary blue_panel-primary " >
            	<div class="panel-heading blue_panel-heading">
                    <h3 class="panel-title blue_panel-title">
						<strong><?php echo $year; ?> Playoffs</strong></h3><h5></h5>
                </div>
                <div class="row">
                  <div class="col-sm-24">
                      <table class="table-condensed" style="width:100%">
                            <tr>
                                <td class="col-md-6"><div class="input-group"><div class="form-control">Team 1</div><span class="input-group-addon"><span class="badge pull-right">1</span></span></div></td>
                                <!--2nd round-->
                                <td class="col-md-6" rowspan="2"><div class="input-group"><div class="form-control"></div><span class="input-group-addon"><span class="badge pull-right"></span></span></div>
                                
                                </td>
                                 
                                <!--3rd round-->
                                <td class="col-md-6" rowspan="4"><div class="input-group"><div class="form-control"></div><span class="input-group-addon"><span class="badge pull-right"></span></span></div>
                                </td>
                            </tr>
                            <tr>
                            
                                <td class="col-md-6"><div class="input-group"><div class="form-control">Team 2</div><span class="input-group-addon"><span class="badge pull-right">1</span></span></div></td>
                                
                                 
                               
                            </tr>
                        
                            <tr>
                                <td class="col-md-6"><div class="input-group"><div class="form-control">Team 3</div><span class="input-group-addon"><span class="badge pull-right">1</span></span></div></td>
                                <!--2nd round-->
                                <td class="col-md-6" rowspan="2"><div class="input-group"><div class="form-control"></div><span class="input-group-addon"><span class="badge pull-right"></span></span></div>
                                </td>
                                <!--Champion-->
                                <td class="col-md-6" rowspan="4"><div class="input-group"><div class="form-control"></div><span class="input-group-addon"><span class="badge pull-right"></span></span></div>
                                </td>
                            </tr>
                            <tr>
                                <td><div class="input-group"><div class="form-control">Team 4</div><span class="input-group-addon"><span class="badge pull-right">0</span></span></div></td>
                            </tr>
                            
                            <tr>
                                <td class="col-md-6"><div class="input-group"><div class="form-control">Team 5</div><span class="input-group-addon"><span class="badge pull-right">1</span></span></div></td>
                                <!--2nd round-->
                                <td class="col-md-6" rowspan="2"><div class="input-group"><div class="form-control"></div><span class="input-group-addon"><span class="badge pull-right"></span></span></div>
                                </td>
                                <!--3rd round-->
                                <td class="col-md-6" rowspan="4"><div class="input-group"><div class="form-control"></div><span class="input-group-addon"><span class="badge pull-right"></span></span></div>
                                </td>
                            </tr>
                            <tr>
                                <td><div class="input-group"><div class="form-control">Team 6</div><span class="input-group-addon"><span class="badge pull-right">0</span></span></div></td>
                            </tr>
                            <tr>
                                <td class="col-md-6"><div class="input-group"><div class="form-control">Team 7</div><span class="input-group-addon"><span class="badge pull-right">1</span></span></div></td>
                                <!--2nd round-->
                                <td class="col-md-6" rowspan="2"><div class="input-group"><div class="form-control"></div><span class="input-group-addon"><span class="badge pull-right"></span></span></div>
                                </td>
                            </tr>
                            <tr>
                                <td><div class="input-group"><div class="form-control">Team 8</div><span class="input-group-addon"><span class="badge pull-right">0</span></span></div></td>
                            </tr>
                      </table>
                  </div>
                </div>
               
            </div>
		</div>
        


       


<?PHP
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/