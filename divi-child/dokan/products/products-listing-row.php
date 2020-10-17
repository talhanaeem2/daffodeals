<?php
    $img_kses = apply_filters( 'dokan_product_image_attributes', array(
        'img' => array(
            'alt'    => array(),
            'class'  => array(),
            'height' => array(),
            'src'    => array(),
            'width'  => array(),
        ),
    ) );

    $row_actions_kses = apply_filters( 'dokan_row_actions_kses', array(
        'span' => array(
            'class' => array(),
        ),
        'a' => array(
            'href'    => array(),
            'onclick' => array(),
        ),
    ) );

    $price_kses = apply_filters( 'dokan_price_kses', array(
        'span' => array(
            'class' => array()
        ),
    ) );
?>
<tr class="<?php echo esc_attr( $tr_class ); ?>">
    <td class="deal-info" data-title="<?php esc_attr_e( 'Deal', 'dokan-lite' ); ?>">
        <?php if ( current_user_can( 'dokan_edit_product' ) ): ?>
            <a href="<?php echo esc_url( dokan_edit_product_url( $post->ID ) ); ?>"><?php echo wp_kses( $product->get_image(), $img_kses ); ?></a>
        <?php else: ?>
            <?php echo wp_kses( $product->get_image(), $img_kses ); ?>
        <?php endif ?>
    
        <div class="deal-title">
            <?php if ( current_user_can( 'dokan_edit_product' ) ): ?>
            <p><a href="<?php echo esc_url( dokan_edit_product_url( $post->ID ) ); ?>"><?php echo esc_html( $product->get_title() ); ?></a></p>
            <?php else: ?>
                <p><a href=""><?php echo esc_html( $product->get_title() ); ?></a></p>
            <?php endif ?>

            <?php if ( !empty( $row_actions ) ): ?>
                <div class="row-actions">
                    <?php echo wp_kses( $row_actions, $row_actions_kses ); ?>
                </div>
            <?php endif ?>
        </div>
        
    </td>
    <td class="post-status" data-title="<?php esc_attr_e( 'Status', 'dokan-lite' ); ?>">
        <label class="dokan-label <?php echo esc_attr( dokan_get_post_status_label_class( $post->post_status ) ); ?>"><?php //echo esc_html( dokan_get_post_status( $post->post_status ) ); ?></label>
    </td>

    <?php //do_action( 'dokan_product_list_table_after_status_table_data', $post, $product, $tr_class, $row_actions ); ?>

    <td data-title="<?php esc_attr_e( 'SKU', 'dokan-lite' ); ?>">
     
    </td>
    <td data-title="<?php esc_attr_e( 'Stock', 'dokan-lite' ); ?>">
       
    </td>
    <td class="diviader"></td>
</tr>
