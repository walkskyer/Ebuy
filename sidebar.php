<div id="right">
    <div class="prod-feed"><a href="<?php echo bloginfo('url');?>"> 进入店铺 </a></div>
    <div class="post-feed"><a href="<?php echo bloginfo('url');?>"> 收藏店铺 </a></div>
    <div class="twitbar"><a href="<?php echo bloginfo('url');?>">订阅我们 </a></div>
    <div class="tabsdiv"><h3 class="sidetitl">
        热门文章 </h3>
        <?php $my_query = new WP_Query('orderby=comment_count&showposts=5');
        while ($my_query->have_posts()) : $my_query->the_post();
        $do_not_duplicate = $post->ID; ?>
        <div class="fblock">
            <?php if (has_post_thumbnail()) { ?>
            <img class="thumbim" alt=""
                src="<?php bloginfo('stylesheet_directory'); ?>/timthumb.php?src=<?php get_image_url(); ?>&amp;h=50&amp;w=50&amp;zc=1"
                />
            <?php } else { ?>
            <img class="thumbim" alt="" width="50px" height="50px"
                 src="<?php bloginfo('template_directory'); ?>/images/dummy.png"/>
            <?php } ?>
            <h3><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h3>
            <p> <?php the_time('F - j - Y'); ?></p>
            <div class="clear"></div>
        </div><?php endwhile; ?>
    </div>
    <!-- 125px banners -->
    <?php include (TEMPLATEPATH . '/sponsors.php'); ?>
    <!-- Sidebar widgets -->
    <div class="sidebar">
        <ul><?php if (function_exists('dynamic_sidebar') && dynamic_sidebar('Sidebar')) : else : ?> <?php endif; ?></ul>
    </div>
</div>