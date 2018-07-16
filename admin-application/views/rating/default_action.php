<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
?>
<!--leftPanel start here-->    
<section class="leftPanel">
	<ul class="leftNavs">
		<li><a href="<?php echo generateUrl('orders');?>">Orders Management</a></li>
		<li><a href="<?php echo generateUrl('rating');?>" class="selected">Ratings & Reviews Management</a></li>
	</ul>
</section>
<!--leftPanel end here--> 
<section class="rightPanel">
	<ul class="breadcrumb">
		<li><a href="<?php echo generateUrl('home');?>"><img src="<?php echo CONF_WEBROOT_URL;?>images/home.png" alt=""> </a></li>
		<li>Manage Ratings & Reviews</li>
	</ul>
	<?php echo Message::getHtml(); ?>
	<div class="title"><h2>Manage Ratings & Reviews</h2></div>
	<section class="box search_filter">
		<!--<div class="box_head"><h3>Search User Ratings </h3></div>-->
		<div class="box_content clearfix">
			<?php
				global $post;
				$frmordersSearch->fill(array('keyword'=>$post['keyword'],'task_status'=>$post['task_status']));
				$frmordersSearch->setFieldsPerRow(5);
				$frmordersSearch->captionInSameCell(true);
				echo $frmordersSearch->getFormHtml();
			?>
		</div>
	</section>
	<section class="box">
		<div class="box_head"><h3>Ratings List </h3></div>
		<div class="box_content clearfix">
			<?php					
				$arr_flds = array(
				'listserial'=>'S.No.',
				'task_ref_id'=>'Order ID',
				//'task_topic'=>'Orders Topic',
				'customer_rating'=>'Customer Ratings',
				'customer_comments'=>'Customer Comments',
				'writer_rating'=>'Writer Ratings',	
				'writer_comments'=>'Writer Comments'
				);
				
				$tbl = new HtmlElement('table', array('width'=>'100%', 'class'=>'dataTable'));
				$th = $tbl->appendElement('thead')->appendElement('tr');
				foreach ($arr_flds as $val) {				
					$e = $th->appendElement('th', array(), $val);			
				}
				 
				foreach ($arr_listing as $sn=>$row){
					$tr = $tbl->appendElement('tr');
					if ($row['task_status'] == 0) $tr->addValueToAttribute('class', 'inactive');
					foreach ($arr_flds as $key=>$val){
						
						switch ($key){
							case 'listserial':
								$td = $tr->appendElement('td');
								$td->appendElement('plaintext', array(), $sn+$start_record);
								break;
							case 'task_topic':
								$td = $tr->appendElement('td');
								$td->appendElement('plaintext', array(), $row['task_topic'] , true);
								break;
							case 'customer_rating':
								$td = $tr->appendElement('td');
								$td->appendElement('plaintext', array(), "<span>Customer Name : </span><h6>".$row['customer']."</h6><br> Delivery Ratings : <div class='ratingsWrap'><span class='p".($row['customer_rating']['delivery_rating']*2)."'></span></div><br> Communication Ratings : <div class='ratingsWrap'><span class='p".($row['customer_rating']['communication_rating']*2)."'></span></div><br> Quality Ratings : <div class='ratingsWrap'><span class='p".($row['customer_rating']['quality_rating']*2)."'></span></div>", true);							
								break;
							case 'customer_comments':
								$td = $tr->appendElement('td', array('width'=>'30%'));
								if(empty($row['customer_comment'])) {
									$customer_comment = 'No review posted!';
								}else {
									$customer_comment = $row['customer_comment'];
									if($row['customer_comment_status']!=1) {
										$customer_comment .= '<br><br><p class="right"><a class="button small red" href="javascript:void(0);" >Declined</a></p>';
									} else {
										if($row['cus_rev_featured']==0) {
										
											$customer_comment .='<br><br><p class="right"><a class="button small orange" href="javascript:void(0);" onclick="markReviewFeatured('.$row['cus_rev_id'].',$(this))">Mark as featured</a></p>';
										} else if($row['cus_rev_featured']==1) {
											$customer_comment .= '<br><br><p class="right"><a class="button small green" href="javascript:void(0);" onclick="markReviewFeatured('.$row['cus_rev_id'].',$(this))">Featured</a></p>';
										}  
									}
								}
								
								$td->appendElement('plaintext', array(), $customer_comment, true);
								break;
							case 'writer_rating':
								$td = $tr->appendElement('td');
								$td->appendElement('plaintext', array(), "<span>Writer Name : </span><h6>".$row['writer']."</h6><br> Delivery Ratings : <div class='ratingsWrap'><span class='p".($row['writer_rating']['delivery_rating']*2)."'></span></div><br> Communication Ratings : <div class='ratingsWrap'><span class='p".($row['writer_rating']['communication_rating']*2)."'></span></div><br> Quality Ratings : <div class='ratingsWrap'><span class='p".($row['writer_rating']['quality_rating']*2)."'></span></div>", true);
								break;
							case 'writer_comments':
								$td = $tr->appendElement('td',array('width'=>'30%'));
								if(empty($row['writer_comment'])) {
									$writer_comment = 'No review posted!';
								}else {
									$writer_comment = $row['writer_comment'];
									if($row['writer_comment_status']!=1) {
										$customer_comment .= '<br><br><p class="right"><a class="button small red" href="javascript:void(0);" >Declined</a></p>';
									} else {
										if($row['wri_rev_featured']==0) {
										
											$writer_comment .='<br><br><p class="right"><a class="button small orange" href="javascript:void(0);" onclick="markReviewFeatured('.$row['wri_rev_id'].',$(this))">Mark As Featured Here</a></p>';
										}else {
											$writer_comment .= '<br><br><p class="right"><a class="button small green" href="javascript:void(0);" onclick="markReviewFeatured('.$row['wri_rev_id'].',$(this))">Featured</a></p>';
										}
									}
								}
								
								$td->appendElement('plaintext', array(), $writer_comment, true);
								break;
							
							default:
								$td = $tr->appendElement('td');
								$td->appendElement('plaintext', array(), $row[$key], true);
								break;
						}
					}
				}
				
				if (count($arr_listing) == 0) $tbl->appendElement('tr')->appendElement('td', array('colspan'=>count($arr_flds)), 'No records found');

				echo $tbl->getHtml();
			?>
			<div class="paginationwrap"><?php echo generateBackendPagingStringHtml($pagesize,$total_records,$page,$pages);?></div>
		</div>
	</section>
</section>