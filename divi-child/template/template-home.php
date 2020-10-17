<?php 

/* Template name: Home */

get_header(); 

$is_page_builder_used = et_pb_is_pagebuilder_used( get_the_ID() );

?>

<div id="main-content">

<?php if ( ! $is_page_builder_used ) : ?>

  <div class="container">
    <div id="content-area" class="clearfix">
      <div id="left-area">

<?php endif; ?>

      <?php while ( have_posts() ) : the_post(); ?>
        <div class="home-banner"><?php the_post_thumbnail('full'); ?></div>
        
 <?php if(!is_user_logged_in()){ ?>
          <div class="deffodeal-subscribe">
           <!-- Begin Mailchimp Signup Form -->
<link href="//cdn-images.mailchimp.com/embedcode/slim-10_7.css" rel="stylesheet" type="text/css">
<style type="text/css">
	/*#mc_embed_signup{background:#fff; clear:left; font:14px Helvetica,Arial,sans-serif; }*/
	/* Add your own Mailchimp form style overrides in your site stylesheet or in this style block.
	   We recommend moving this block and the preceding CSS link to the HEAD of your HTML file. */
</style>
<div id="mc_embed_signup">
<form action="https://daffodeals.us2.list-manage.com/subscribe/post?u=4895be64b965b63d1da585a83&amp;id=f2bb213ff6" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
    <div id="mc_embed_signup_scroll">
	<label for="mce-EMAIL">Subscribe</label>
	<input type="email" value="" name="EMAIL" class="email" id="mce-EMAIL" placeholder="email address" required>
    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
    <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_4895be64b965b63d1da585a83_f2bb213ff6" tabindex="-1" value=""></div>
    <div class="clear"><input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button"></div>
    </div>
</form>
</div>

<!--End mc_embed_signup-->
          </div>
        <?php } ?>


        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

        <?php if ( ! $is_page_builder_used ) : ?>

          <h1 class="entry-title main_title"><?php the_title(); ?></h1>
        <?php
          $thumb = '';

          $width = (int) apply_filters( 'et_pb_index_blog_image_width', 1080 );

          $height = (int) apply_filters( 'et_pb_index_blog_image_height', 675 );
          $classtext = 'et_featured_image';
          $titletext = get_the_title();
          $alttext = get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true );
          $thumbnail = get_thumbnail( $width, $height, $classtext, $alttext, $titletext, false, 'Blogimage' );
          $thumb = $thumbnail["thumb"];

          if ( 'on' === et_get_option( 'divi_page_thumbnails', 'false' ) && '' !== $thumb )
            print_thumbnail( $thumb, $thumbnail["use_timthumb"], $alttext, $width, $height );
        ?>

        <?php endif; ?>

          <div class="entry-content">
          <?php
            the_content();

            if ( ! $is_page_builder_used )
              wp_link_pages( array( 'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'Divi' ), 'after' => '</div>' ) );
          ?>
          </div> <!-- .entry-content -->

        <?php
          if ( ! $is_page_builder_used && comments_open() && 'on' === et_get_option( 'divi_show_pagescomments', 'false' ) ) comments_template( '', true );
        ?>

        </article> <!-- .et_pb_post -->

      <?php endwhile; ?>

<?php if ( ! $is_page_builder_used ) : ?>

      </div> <!-- #left-area -->

      <?php get_sidebar(); ?>
    </div> <!-- #content-area -->
  </div> <!-- .container -->

<?php endif; ?>

</div> <!-- #main-content -->

<?php

get_footer();