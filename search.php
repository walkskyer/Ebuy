<?php get_header(); ?>

<ul class="breadcrumbs clearfix">
	
		
<li>			<?php
				$mySearch =new WP_Query("s=$s & showposts=-1");
				$num = $mySearch->post_count;
				echo $num.' 个搜索结果 '; the_search_query();
			?> 用时 <?php  get_num_queries(); ?> <?php timer_stop(1); ?> 秒.</li>
		
		
</ul>

<div id="content" >
<?php if (have_posts()) : ?>
<?php while (have_posts()) : the_post(); ?>
		
<div class="post" id="post-<?php the_ID(); ?>">
<div class="title">
<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
</div>
<div class="entry">
<?php wpe_excerpt('wpe_excerptlength_index', ''); ?>
<div class="clear"></div>
</div>


</div>
<?php endwhile; ?>

<?php getpagenavi(); ?>

<?php else : ?>

<div class="post">
<div class="title">
		<h2>您搜索的 - <?php the_search_query();?> - 没有在我们的店铺中找到.</h2>
</div>


<div class="entry">
<p>我们的建议:</p>
<ul>
   <li>  确保词组拼写正确.</li>
   <li>  尝试其他的关键词.</li>
   <li>  尝试更广泛的关键词.</li>
</ul>
</div></div>


<?php endif; ?>

</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>