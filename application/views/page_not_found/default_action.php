<div class="pageBar">
  	<div class="fix-container">
    	<div class="grid_1">
            <h2><?php echo Utilities::getLabel( 'L_404_Error' ); ?></h2>
            <ul class="breadcrumb">
                <li><a href="<?php echo CONF_WEBROOT_URL;?>" class="home"><img src="images/home_icon.png" alt=""></a></li>
                <li><span><?php echo Utilities::getLabel( 'L_404_Error' ); ?></span></li>
            </ul>
        </div>
        
        <div class="grid_2">
        	
        </div>
    	
    </div>
  </div>
  <!--page bar end here-->
  
  
  <!-- body -->
  <div id="body">
    
    <div class="sectionInner clearfix">
    
        <div class="fix-container">
        
        	<!--error section-->
                <div class="errorWrap clearfix">
                    <div class="textwrap">
                        <div class="text">
                            <span><?php echo Utilities::getLabel( 'L_404' ); ?></span>
                            <div class="overtext">
                                <h2><?php echo Utilities::getLabel( 'L_Error' ); ?></h2>
                                <h5><?php echo Utilities::getLabel( 'L_404_Error_Page_Description' ); ?></h5>
                            </div>
                        </div>
                        <img src="images/error1.png" alt="" class="errorpic" />
                    </div>
                    <div align="center">
                    <a href="<?php echo CONF_WEBROOT_URL;?>" class="buttonGreen"><?php echo Utilities::getLabel( 'L_Back_to_Home' ); ?></a>
                    </div>
                </div>
            <!--error section-->
            
        </div>
    
   </div> 
    
  </div>