<div id="search">
    <form method="get" id="searchform" action="<?php bloginfo('home'); ?>">
        <input id="s" type="text" name="s" onfocus="if(this.value=='搜索本店商品...'){this.value=''};"
               onblur="if(this.value==''){this.value='搜索本店商品...'};"
               value="<?php $searchValue=wp_specialchars($s, 1); echo $searchValue?$searchValue:'搜索本店商品...'?>"/>

    </form>
</div>
<div class='clear'></div>	
