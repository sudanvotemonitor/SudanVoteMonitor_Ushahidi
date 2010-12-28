<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<title><?php echo $site_name; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<?php echo $header_block; ?>
	<?php
	// Action::header_scripts - Additional Inline Scripts from Plugins
	Event::run('ushahidi_action.header_scripts');
	?>
</head>

<body id="page">
	<!-- wrapper -->
	<div class="rapidxwpr floatholder">

		<!-- header -->
		<div id="header">

			<!-- follow us on twitter -->
			<div id="FollowUSonTwitter"><a href="http://www.twitter.com/SudanVoteMon"><img src="http://twitter-badges.s3.amazonaws.com/follow_us-b.png" alt="Follow SudanVoteMon on Twitter"/></a>
			</div>
			<!-- / follow us on twitter -->

			<!-- searchbox -->
			<div id="searchbox">
				<!-- languages -->
				<?php echo $languages;?>
				<!-- / languages -->

				<!-- searchform -->
				<?php echo $search; ?>
				<!-- / searchform -->

			</div>
			<!-- / searchbox -->

			<!-- logo -->
			<div id="logo">
			</div>
			<!-- / logo -->
			
			<!-- rolling picture -->
			<script language="JavaScript" src="themes/sudan/ImageChanger/magicimage.js" type="text/javascript"></script>
			<div id="MagicImage"></div>
			<!-- / rolling picture -->			
			
			<!-- submit incident -->
			<?php echo $submit_btn; ?>
			<!-- / submit incident -->
			
		</div>
		<!-- / header -->

		<!-- main body -->
		<div id="middle">
			<div class="background layoutleft">

				<!-- mainmenu -->
				<div id="mainmenu" class="clearingfix">
					<ul>
						<?php nav::main_tabs($this_page); ?>
					</ul>

				</div>
				<!-- / mainmenu -->
