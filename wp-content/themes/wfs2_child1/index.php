<?php


// INDEX.PHP FOR HUBWEB1

/*
All widget and library stuff is now in plugins. All that's needed in index.php are the calls to output the widgets
eg: echo get_region_html('top');

All plugin-dependent calls to functions and classes should be wrapped in "if(class/function_exists() {}" to
prevent lock-out when plugins are deactivated.

*/



if(class_exists('Wf_Widget') && isset(Wf_Widget::$region_html['right'])) {
// Pre-loading the sidebar
	if(is_page() && empty(Wf_Widget::$region_html['right']) && class_exists('Post_widget')) {
		$post_obj = new Post_widget('right default', 'post', 'show_title=true&ids=37&widget=post');
		Wf_Widget::$region_html['right'] = $post_obj->get_html(); 
	}
	if(class_exists('Comment_form_widget')) { // && isset(Wf_Widget::$region_html['bottom']) 
	// Pre-loading the comments widgets
		$comment_obj = new Comments_widget('right', 'comments', 'heading=Comments on this page&date_format=j.n.y  g:i A&post_id=current&widget=comments');
		$comment_form_obj = new Comment_form_widget('right', 'comment_form', 'heading=Add a comment?&widget=comment_form');
		Wf_Widget::$region_html['right'] = Wf_Widget::$region_html['right']."</div><div class='wf_lining'>".$comment_obj->get_html().$comment_form_obj->get_html(); 
	}

}



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
	
	<body <?php body_class(page_class($post));?>>
				
		
		<header id="header">
		
		<div id="header_lining" class="container">
		
			<img id='chaco_headpic' src="<?php echo THEME_FOLDER_URL ?>/images/chaco_header3.jpg" width="1008" height="300" alt='Playing field'>
			
			<div id='brand'>
				<p id='sitename'><?php echo bloginfo('name'); ?></p>
				<p id='strapline'><?php echo bloginfo('description'); ?></p>
				<a id='brand_logo' href='<?php echo bloginfo('url'); ?>'>
					<img id='ex_chaco_logo' src="<?php echo THEME_FOLDER_URL ?>/images/chaco_logo.png" width="200" height="104" alt='Leeds for Change logo'>
				</a>
			</div>
					
			<div class='insert_items region_topnav default'>
				<div class='postwrap'>
					<ul id='topnav' class='inline'><?php
						//echo $topnav; ?>
						
					</ul>
					
				</div>
			</div>
			
		</div>
							
			  <nav id='nav_container' class="container navbar">
				
				<div class="navbar-header">
				<?php echo get_navbar_user(); ?>
				  <button type="button" class="navbar-toggle collapsed button" data-toggle="collapse" data-target="#navmain">
					Main Menu
				  </button>
				  
				</div>
			
				<!-- Collect the nav links, forms, and other content for toggling -->
				<div class="collapse navbar-collapse" id="navmain">
				  <ul class="nav navbar-nav">
					<?php echo $menu;  ?>
				  </ul>
			
				</div><!-- /.navbar-collapse -->
			  </nav><!-- /.container-fluid -->			
			
		</header>
		
		
		<br class='clearboth'/>
		
		
		<div id="twocols" class="container"><?php // v2.26
			echo get_region_html('top'); 
			echo get_region_html('twocol'); // v5.3 doesn't now have to be a previously declared region ?>
		
			<div class="row-fluid row">
			
					
				<?php // MAINCOL /////////////////////////////////////////////////////////////////////// ?>
			
				<!--<div id='maincol' class="col-sm-8 span8">-->	<?php // v2.26
					if(is_front_page() && isset($custom_content)) { // || is_page(2737)) { // home_test
						echo "<div id='maincol' class='col-md-8'>";// v2.28
						echo $custom_content;
						echo get_region_html('bottom'); 
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
									if(function_exists('yoast_breadcrumb')) {
										yoast_breadcrumb('<p id="breadcrumbs">','</p>');
									}
									if (have_posts()) : while (have_posts()) : the_post(); // Main loop
										//if(function_exists('wf_privspan_the_title')) { // prevent crash when wf_library deactivated
										
										echo "<div class='wf_post'>";
										if(is_single() || is_page() ) {
											
											if(in_category('blog')) {
												//echo "<p class='pre-title'>Blog</p>";	
												echo get_blog_pre_title_bits($post);;
											}
											echo "<h1>".l4c_the_title($post)."</h1>";
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
						echo "<div id='rightcol' class='col-sm-4'>";// NB: change to col-md-4 if nec
					} else {
						echo "<div id='rightcol' class='col-sm-4'>";
					}

					
					echo "<div class='wf_lining'>";
					echo get_region_html('right');
					if(isset($right_stuff)) {
						echo $right_stuff;
					}
					echo "</div><!-- wf_lining -->";
					?>
				</div><!--right col -->
				
				
				
			</div><!--row-fluid -->
		</div><!-- twocols -->
	
			
			
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