<?PHP
	/**
	 * content_header view.
	 *
	 */


	//loads the content of whatever page needs to be loaded by the content selector
	//and places it into a div called ajax_display_area that must be included in 
	//a page that needs to use this feature.
//d($this->_ci_cached_vars);
?>
	<script type="text/javascript">
	
		function change_content(path,display){	
			$('#ajax_display_area').load(path);
			$('#dropdown_title').html(display);
			
			//pusher.disconnect();
		}
		
		$(document).on("ready", function() { change_content('<? 
			if(!isset($dropdown_title)){
              	$dropdown_title='';
              }
              echo base_url().$load_path."','".$dropdown_title; ?>'); 
		});
	</script>
	<!--VIEW CONTENT HEADER: THIS PORTION SHOULD BE AT TOP OF EVERY VIEW--><head>
		<!--Gets the title of page from controller -->
		<title><?php echo $title; ?></title>
	</head>
    <div class="container-fluid" id="container"><!--creates white box around content with red border-->        
        <div class="row " >  
            <div class="col-xs-24 " >
            	
                    <div class="col-xs-18 page_title" style="padding-top:9px">
                    
                       	 <?php echo $title; ?> 
                    </div><!--page title-->    
					<?
					if(isset($content_selector))
					{ ?>
						<div id="content_selector" class="col-xs-6 pull-right page_title">
							
                                <button class="btn btn-default pull-right dropdown-toggle" type="button" id="content_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <span id="dropdown_title"><? echo $display_page; ?></span>
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu"  aria-labelledby="dropdownMenu1">
                                    <?php
                                    foreach($content_selector as $display => $path)
                                    {
                                        ?><li><a href="#" onClick="change_content('<? echo $path."','".$display; ?>')" ><? echo $display; ?></a></li>
                                    <? } ?>
                                </ul>
                           
                		</div><!--content_selector-->
                
            		<? } ?>
					<div class="col-xs-24" style="border-top:2px solid #333; "></div>
               
            </div>
  
        </div><!-- end row -->
  
    <!--end view content header, follow iwth "content_area"-->
			
			<?PHP 
/*End of file content_header.php*/
/*Location: ./application/veiws/content_header.php*/