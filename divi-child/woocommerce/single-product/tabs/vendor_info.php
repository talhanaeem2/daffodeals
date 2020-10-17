<?
global $post;
$author_id = $post->post_author;

$first_name = get_the_author_meta( 'first_name' , $author_id ); 
$last_name = get_the_author_meta( 'last_name' , $author_id ); 
$name = !empty($last_name)?$first_name.'&nbsp;'.$last_name:$first_name;

$store_info = dokan_get_store_info( $author_id );
?>

<ul class="list-unstyled">
    <li><span><strong>Vendor Name:</strong> </span> <span class="details"><?php echo $store_info['store_name'];  ?></span></li>
    <li><span><strong>Rating:</strong>  </span> <span class="details"><?php echo seller_get_readable_rating($author_id); ?></span></li>
</ul>