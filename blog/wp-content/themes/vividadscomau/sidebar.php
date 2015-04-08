<?php
/**
 * The sidebar containing the secondary widget area
 *
 * Displays on posts and pages.
 *
 * If no active widgets are in this sidebar, hide it completely.
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */

if ( is_active_sidebar( 'sidebar-2' ) ) : ?>
	<div id="tertiary" class="sidebar-container" role="complementary">
		<div class="sidebar-inner">
      		<div class="widget-area">
				<?php dynamic_sidebar( 'sidebar-2' ); ?>
                                
			</div><!-- .widget-area -->
		</div><!-- .sidebar-inner -->
	</div><!-- #tertiary -->
<?php endif; ?>


<?php
    // Get the ID of a given category
    $category_id = get_cat_ID('Case Studies');
    // Get the URL of this category
    $category_link = get_category_link($category_id);
    $category_id1 = get_cat_ID('Education');
    // Get the URL of this category
    $category_link1 = get_category_link($category_id1);
    $category_id2 = get_cat_ID( 'Videos' );
    // Get the URL of this category
    $category_link2 = get_category_link($category_id2);
	
	
?>

<!-- Print a link to this category -->
<!--<ul>
<li><a href="<?php echo esc_url( $category_link ); ?>" title="Category Name">Case Studies</a></li>
<li><a href="<?php echo esc_url( $category_link1 ); ?>" title="Category Name">Education – We Guide</a></li>
<li><a href="<?php echo esc_url( $category_link2 ); ?>" title="Category Name">Videos</a></li>
</ul>-->