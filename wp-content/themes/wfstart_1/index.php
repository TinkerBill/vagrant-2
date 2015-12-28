<?php


// INDEX.PHP FOR HUBWEB1

/*
All widget and library stuff is now in plugins. All that's needed in index.php are the calls to output the widgets
eg: echo get_region_html('top');

All plugin-dependent calls to functions and classes should be wrapped in "if(class/function_exists() {}" to
prevent lock-out when plugins are deactivated.

*/




// v2.27 moved here so that don't get to see menu being created
$menu = wp_list_pages('sort_column=menu_order&title_li=&depth=3&echo=0&exclude='.get_page_excludes()); 
$menu = str_replace('/cms/', '/', $menu);

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<!--<head profile="http://gmpg.org/xfn/11">-->
	<head>
		<meta charset="<?php bloginfo('charset'); ?>" >
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="description" content="A hub website for resourcing and linking change-makers in Leeds" >
		<meta name="keywords" content="solidarity, equality, justice, social change, Leeds, activism, activist, campaigning, lobbying, environment, poverty">
		<base id="htmlbase" href="<?php echo WP_HOME.'/'; // BILL 14/2/14 ?>" >
		<title><?php wp_title('&laquo;', true, 'right'); ?><?php bloginfo('name'); ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0"><!-- Bootstrap -->
		<meta name="google-site-verification" content="p7Kr8Ytk_3bz7m6tYSKdgs2YfHJfmT8u5K2XuUO2HpY" />
		<style media="screen">@import url( <?php bloginfo('stylesheet_url'); ?> );</style>
		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" >
		<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
		<link rel="icon" href="/favicon.ico" type="image/x-icon">
		<!--[if lt IE 9]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<?php wp_head(); // required for plugins that hook to 'wp_head' ?>
		
		<!-- -->
	</head>
	
	<body <?php body_class();?>>
		
		
			<header id="header">
			
			<div id="header_lining" class="container">
						
				<div class='insert_items region_topnav default'>
					<div class='postwrap'>
						<ul id='topnav' class='inline'><?php
							//echo $topnav; ?>
							
						</ul>
						<p id='strapline'><?php echo bloginfo('description'); ?></p>
					</div>
				</div>
				<div id='l4c_logo'>
					<img  src="<?php echo THEME_FOLDER_URL ?>/images/l4c_logo_200.png" width="200" height="104" alt='Leeds for Change logo'>
					<!--<p id='beta'>Beta Release</p>-->
				</div>
				<img id='lfc_headpic' src="<?php echo THEME_FOLDER_URL ?>/images/Lfc-head-pic_v2.png" width="502" height="135" alt='Assorted thumbnail photos of Leeds campaigners'>
				
			</div>
				
								
				<nav id='nav_wrapper' class="navbar navbar-default">
				  <div class="container">
					
					<div class="navbar-header">
			
					  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navmain">
						Main Menu
					  </button>
					  
					</div>
				
					<!-- Collect the nav links, forms, and other content for toggling -->
					<div class="collapse navbar-collapse" id="navmain">
					  <ul class="nav navbar-nav">
						<?php echo $menu;  ?>
					  </ul>
				
					</div><!-- /.navbar-collapse -->
				  </div><!-- /.container-fluid -->
				</nav>
				
				
			</header>
			
			
			<br class='clearboth'/>
			
			<!--<div id='outerwrap' class="container">
			
			
			<div id="threecols" class="row">-->
				<!--<div id="twocols" class="col-sm-12 span12">-->
				<div id="twocols" class="container"><?php // v2.26
					echo get_region_html('top'); 
					echo get_region_html('twocol'); // v5.3 doesn't now have to be a previously declared region ?>
				
					<div class="row-fluid row">
					
							
						<?php // MAINCOL /////////////////////////////////////////////////////////////////////// ?>
					
						<!--<div id='maincol' class="col-sm-8 span8">-->	<?php // v2.26
							if(is_front_page() && isset($custom_content)) { // || is_page(2737)) { // home_test
								echo "<div id='maincol' class='col-md-8'>";// v2.28
								echo $custom_content;
							} else { ?>
								<!--<div class="wf_lining">--><?php
									echo "<div id='maincol' class='col-sm-8'>";// v2.28
									if(is_404()) { ?>
										<div class="wf_lining">
										<h1>Ooops!</h1>
										<p>Sorry &ndash; We can&rsquo;t seem to find that page.</p>
										<p>Please navigate to your chosen page using the main menu above.</p>
										<p>&nbsp;</p>
										<p>&nbsp;</p><?php 
						
									} else { //It's not a 404
										if(isset($custom_content)) {
											//echo "Yes";
											echo $custom_content; // eg: from single-l4c_group.php
										} else {
											echo "<div class='wf_lining'>";
											if (have_posts()) : while (have_posts()) : the_post(); // Main loop
												//if(function_exists('wf_privspan_the_title')) { // prevent crash when wf_library deactivated
												
												echo "<div class='wf_post'>";
												if(is_single() || is_page() ) {
													
													if(in_category('blog')) {
														echo "<p class='pre-title'>Blog</p>";	
														//echo get_blog_pre_title_bits($post);;
													}
													echo "<h1>".get_the_title($post)."</h1>";
												} // is_single() || is_page()
												if(is_archive()) {
													echo "<h2 class='h1'>".l4c_the_title($post)."</h2>";
												}
													
												the_content(); 
												echo "</div>"; // .wf_post
											endwhile; endif;  // end of main loop
										}
										echo get_region_html('bottom'); 
										
										if(is_category()){ // do the pagination links
											echo "<!--<div class='wf_lining'>-->
											<p>Category</p>";
											global $wp_query;
											$total_pages = $wp_query->max_num_pages;
											if ($total_pages > 1){
											  $current_page = max(1, get_query_var('paged'));
											  echo '<nav class="page_nav">';
											  echo paginate_links(array(
												  'base' => get_pagenum_link(1) . '%_%',
												  'format' => '/page/%#%',
												  'current' => $current_page,
												  'total' => $total_pages,
												  'prev_text' => 'Prev',
												  'next_text' => 'Next'
												));
											  echo '</nav>';
											}
										}
										
									} 
									
									?>
								</div><!-- wf_lining --><?php
							} ?>
						</div><!-- maincol--><?php
						
							// RIGHTCOL ///////////////////////////////////////////////////////////////////////	  
							
							if(is_front_page()) { // v2.28
								echo "<div id='rightcol' class='col-md-4'>";// v2.28
							} else {
								echo "<div id='rightcol' class='col-sm-4'>";// v2.28
							}

							if(isset($right_stuff)) {
								echo $right_stuff;
							}
							echo get_region_html('right');
							?>
								
						</div><!--right col -->
						
						
						
					</div><!--row-fluid -->
					<!--<div id="twocolclearer"></div>-->
				</div><!-- twocols -->
			
			
			
				<?php // LEFTCOL ///////////////////////////////////////////////////////////////////////	  ?>
				<div id="leftcol" class="span3" style="display: none;">
					<div class="wf_lining"> <?php 
					
						echo get_region_html('left'); 
						?>
					</div><!-- wf_lining -->
				</div><!-- leftcol -->
				
				
				<!--<div id="threecolclearer"></div> -->
			<!--</div>--><!-- threecols -->
			
			<footer id="footer">
				<?php if(function_exists('admin_login_link')) { // wf_library
					admin_login_link();
				}?>
				<p>Powered by WordPress. <a href="http://www.wingfinger.co.uk/">Web design by Wingfinger Graphics, Leeds</a></p>
			</footer>
			<?php wp_footer(); // required for plugins that hook to 'wp_footer'  
			
			if(class_exists('Wf_Debug')) { // wf_library
				echo Wf_Debug::output('debug'); // v6.17 
			}?>
		
		
	</body>
</html>