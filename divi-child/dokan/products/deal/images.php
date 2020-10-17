<?php if ( ! defined('ABSPATH')) exit('No direct script access allowed'); 
if ( isset( $_GET['product_id'] ) ) {
    $post_id        = intval( $_GET['product_id'] );
}
?>
<h4><?php esc_attr_e( 'Deal Images', 'dokan-lite' ); ?></h4>
<p><?php esc_attr_e( 'These images will be the face of your deal — please include the highest-quality images available', 'dokan-lite' ); ?>.</p>

<div class="content-half-part featured-image" id="deal_image_id">
    <div class="left_dokan_img">
        <div class="dokan-feat-image-upload dokan-new-product-featured-img">
            <label><?php esc_attr_e( 'Main Deal Image', 'dokan-lite' ); ?></label>
            <?php
            $wrap_class        = ' dokan-hide';
            $instruction_class = '';
            $feat_image_id     = 0;

            if ( has_post_thumbnail( $post_id ) ) {
                $wrap_class        = '';
                $instruction_class = ' dokan-hide';
                $feat_image_id     = get_post_thumbnail_id( $post_id );
            }
            ?>
            <div class="instruction-inside<?php echo esc_attr( $instruction_class ); ?>">
                <input type="hidden" name="feat_image_id" class="dokan-feat-image-id" value="<?php echo esc_attr( $feat_image_id ); ?>">

                <i class="fa fa-cloud-upload"></i>
                <a href="#" class="dokan-feat-image-btn btn btn-sm"><?php esc_html_e( 'Upload a product cover image', 'dokan-lite' ); ?></a>
            </div>
            <div class="image-wrap<?php echo esc_attr( $wrap_class ); ?>">
                
                <a class="close dokan-remove-feat-image" title="<?php esc_attr_e( 'Delete image', 'dokan-lite' ); ?>"><span>&times;</span></a>
                <?php if ( $feat_image_id ) { ?>
                    <?php 
                    $feat_image = wp_get_attachment_url( get_post_thumbnail_id($post_id) ); ?>
                        <a class="dokan-zoom-feat-image zoomimg" title="Crop image" pid="<?php echo $post_id; ?>" imgid="<?php echo $feat_image_id; ?>" imgnonce="<?php echo wp_create_nonce( "kc_image_crop" ); ?>" isfeat="1" datasrc="<?php echo $feat_image; ?>"><span><i class="fa fa-crop" aria-hidden="true"></i></span></a>
                        <img height="" width="" id="<?php echo $feat_image_id; ?>" src="<?php echo $feat_image; ?>" alt="">
                <?php } else { ?>
                    <img height="" width="" src="" alt="">
                <?php } ?>
            </div>
        </div><!-- .dokan-feat-image-upload -->
    </div>

    <?php      
    if ( apply_filters( 'dokan_product_gallery_allow_add_images', true ) ): ?>
        <div class="dokan-product-gallery" id="image_gallery">
            <label><?php esc_attr_e( 'Additional Images', 'dokan-lite' ); ?></label>
            <div class="dokan-side-body" id="dokan-product-images">
                <div id="product_images_container">
                    <ul class="product_images dokan-clearfix">
                        <?php

                        $product_images = get_post_meta( $post_id, '_product_image_gallery', true );
                        
                        $gallery = explode( ',', $product_images );

                        if ( $gallery ) {
                            foreach ($gallery as $image_id) {
                                if ( empty( $image_id ) ) {
                                    continue;
                                }

                                $attachment_image = wp_get_attachment_image_src( $image_id, 'thumbnail' );
                                $attachment_image2 = wp_get_attachment_image_src( $image_id, 'full' );
                                ?>
                                <li class="image" data-attachment_id="<?php echo esc_attr( $image_id ); ?>">
                                    <div class="image_hover_deal">
                                        <img src="<?php echo esc_url( $attachment_image[0] ); ?>" alt="">
                                        <a href="#" class="action-delete" title="<?php esc_attr_e( 'Delete image', 'dokan-lite' ); ?>">
                                            <span>&times;</span> 
                                        </a>
                                        <a class="dokan-zoom-feat-image zoomimg hide" title="Crop image" pid="<?php echo $post_id; ?>" imgid="<?php echo $image_id; ?>" imgnonce="<?php echo wp_create_nonce( "kc_image_crop" ); ?>" isfeat="0" datasrc="<?php echo esc_url( $attachment_image2[0] ); ?>"><span><i class="fa fa-crop" aria-hidden="true"></i></span></a>
                                    </div>
                                </li>
                                <?php
                            }
                        }
                        ?>
                        <li class="add-image add-product-images tips" data-title="<?php esc_html_e( 'Add More image', 'dokan-lite' ); ?>">
                            <div class="image_hover_deal">
                                <a href="#" class="add-product-images">
                                    <span class="hover_effect_deal"><i class="fa fa-plus" aria-hidden="true"></i>
                                    <span><?php esc_html_e( 'Add More', 'dokan-lite' ); ?></span></span>
                                </a>
                            </div>
                        </li>
                    </ul>

                    <input type="hidden" id="product_image_gallery" name="product_image_gallery" value="<?php echo esc_attr( $product_images ); ?>">
                </div>
            </div>
        </div> <!-- .product-gallery -->
    <?php endif; ?>
</div><!-- .content-half-part -->  