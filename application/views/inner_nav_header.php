<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
?>
<?php
	$logoUrl = generateUrl('image', 'logo', array( CONF_WEBSITE_LOGO ));
?>
<div class="row page-container">
	<div class="page-sidebar <?php if(!User::isWriter()) {echo "green-bg";}?>">
		<figure class="db-logo mob-hide">
			<a href="<?php echo generateUrl('');?>">
				<img src="<?php echo $logoUrl;?>" alt="logo">
			</a>
		</figure>
		<ul class="db-nav">
			<?php
				echo getPageTabs($ord_count);
			?>
		</ul>
	</div>
	<div class="db-content">
		