<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!-- ----------------------------------------------------------------------------------------------- 
Begin footer
-------------------------------------------------------------------------------------------------- -->


		
    <?php //if(isset($_SESSION['team_id']) && $_SESSION['team_id']==56) {d($_SESSION);} ?>
    							<!--footter acknowledgments-->
                                
                                	<div class="col-xs-24 text-center" style="background-color:#333; border-bottom-left-radius:10px;border-bottom-right-radius:10px;padding:15px;">
                                    	<div><span style="color:white"><strong>The FFFL&nbsp;&nbsp;&nbsp;<? $image_properties = array(
														'src'   => base_url().'assets/img/logos/fffl_logo.gif',
														'width' => '50',
													);
													echo img($image_properties); ?> Since 1998</strong></span></div>
                                                    <div style="margin-bottom:15px;"><span style="color:white"><strong>Commissioner: Shane Cole</strong></span></div>
                                    	<div>
                                        	<span style="color:white"><strong>Site Powered By:</strong></span> 
                                        </div>
                                        <div>
										<? echo '<a href="http://codeigniter.com">'.img(base_url().'assets/img/codeigniter.png',FALSE,'height="55px" style="padding:10px;"').'</a>'; ?>
                                        <? echo '<a href="http://getbootstrap.com/">'.img(base_url().'assets/img/bootstrap-logo.png',FALSE,'height="55px" style="padding:10px;"').'</a>'; ?>
                                        <? echo '<a href="http://api.fantasy.nfl.com/">'.img(base_url().'assets/img/nfl_logo.png',FALSE,'height="55px" style="padding:10px;"').'</a>'; ?>
                                        <? echo '<a href="http://tympanus.net/codrops/2013/08/13/multi-level-push-menu/">'.img(base_url().'assets/img/tympanus.png',FALSE,'width="55px" style="padding:10px;"').'</a>'; ?>
                                        </div>
                                    </div>
                                
                                <div class='device-check visible-xs' data-device='xs'><h3>xs</h3></div>
                                <div class='device-check visible-sm' data-device='sm'><h3>sm</h3></div>
                                <div class='device-check visible-md' data-device='md'><h3>md</h3></div>
                                <div class='device-check visible-lg' data-device='lg'><h3>lg</h3></div>  
                                
                                	<div class="col-xs-24 text-center">
                                    <? 
										 if($view=='home'){ ?>

										<script type="text/javascript">
                                            google_ad_client = "pub-1977747528300171";
                                            /* 300x250, created 9/13/08 */
                                            google_ad_slot = "6946401653";
                                            google_ad_width = 300;
                                            google_ad_height = 250;
                                            
                                        </script>
                                        <script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
                                        </script>
                                        <? } ?>
                                	</div>
                               
                            </div><!--bootstrap content-->
                        </div><!-- menu content clearfix -->
                    </div><!-- /scroller-inner -->
                </div><!-- /scroller -->
			</div><!-- /pusher -->

            <!--modal to tell user to rotate device-->
            <div class="modal fade" tabindex="-1" role="dialog" id="rotate_modal">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-body" style="text-align:center">
                    <p>
                        <?php 
                            $image_properties = array(
                                'src'   => base_url().'assets/img/rotate.png',
                                'height' => '',
                            );
                
                            echo img($image_properties).'&nbsp;'; 
                        ?>
                        <br>Please rotate your device
                    </p>
                  </div>
                </div><!-- /.modal-content -->
              </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
            
            <!--front modal-->
            <? if(isset($_SESSION['team_id'])){ ?>
            <div>
                <div class="modal fade" tabindex="-1" role="dialog" id="front_modal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body" style="text-align:center">
                                <h3>Please support the site with an ad click.</h3>
                                <div class="row">
                                    <div class="col-xs-24 text-center">
                                        
                                            <script type="text/javascript">
                                                google_ad_client = "pub-1977747528300171";
                                                
                                                google_ad_slot = "6946401653";
                                                google_ad_width = 300;
                                                google_ad_height = 250;
                                                
                                            </script>
                                            <script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
                                            </script>
                                           
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
            </div>
			<? } ?>
            <!-- this is an erea to load modals for pages -->
            <div id="modal_area"></div>
 
			
		</div><!-- /menu container -->
		
		

        <!-- Bootstrap core JavaScript-->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="<?php echo base_url(); ?>assets/js/bootstrap/bootstrap.min.js"></script>
        
        <!--menu script -->
		<script src="<?php echo base_url(); ?>assets/js/menu/classie.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/menu/mlpushmenu.js"></script>
		<script>
			new mlPushMenu( document.getElementById( 'mp-menu' ), document.getElementById( 'trigger' ), {
				type : 'cover'
			} );
		</script>

        <!--A script to add a modal in small devices telling user to rotate -->
       
        <script>
            
            $(document).ready(function() {
            
				<? if($ads == 1){ ?>
					$('#front_modal').modal('show');
				<? } ?>
                var $window = $(window);
                var kbinactive_width = 0;           
                function check_width() {
                    var window_width = $window.width();
					var window_height = $window.height();
                    
                
                    if(window_height < window_width && window_height < 490 && (kbinactive_width==0 || kbinactive_width != window_width)){
                        $('#rotate_modal').modal('show');
                    } else {
                        $('#rotate_modal').modal('hide');
						if(kbinactive_width==0) {
							kbinactive_width = window_width;	
						}
                    }
                }
                // Execute on load
                check_width();
                // Bind event listener
                $(window).resize(check_width);
            });
		</script>
        
         

	</body>
</html>