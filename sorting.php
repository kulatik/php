<ul class="popular_article">
	<?php $popular = new WP_Query(array('posts_per_page'=>3, 'meta_key'=>'popular_posts', 'orderby'=>'meta_value_num', 'order'=>'DESC'));
	while ($popular->have_posts()) : $popular->the_post(); ?>
            <div class="articles_image">
            <?php if ( has_post_thumbnail()) { ?>
   <?php the_post_thumbnail(''); ?>
           
 <?php } ?>
        <h3><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h3>
     
			<div class="category-data"><div class="category-dataline_blog">
<?php
    $categories = get_the_category(); 
    if($categories[0]){
        echo '<a href="' . get_category_link($categories[0]->term_id ) . '">'. $categories[0]->name . '</a>';
    }
?>
<p><?php the_time('d.m.Y') ?></p>
</div></div>
	<?php endwhile; wp_reset_postdata(); ?>
</ul>