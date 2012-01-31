<!-- Navigation -->
<div class="navigation container_12">
	<div class="grid_12">
		<noscript>
            <ul class="noscript">
                <?php 
                if ($article != '') {
                    $category = $article->getCategoryCat();
                } else if ($_GET['cat'] != '') {
                    $category = $_GET['cat'];
                }
                    
                $sql = "SELECT label,cat FROM `category` 
                    WHERE hidden=0 
                    AND id>0 
                    ORDER BY id ASC";
                $cats = $db->get_results($sql);
                foreach($cats as $key => $cat) { ?>
                    <li class="<?php echo $cat->cat; ?> <?php if($category==$cat->cat) echo 'selected'; ?> <?php if($cat->cat == 'news') echo 'first'; ?> <?php if($cat->cat == 'sport') echo 'last'; ?>">
                        <a href="<?php echo STANDARD_URL.$cat->cat; ?>/">
                            <?php echo $cat->label; ?>
                        </a>
                    </li>
                <?php } ?>	
            </ul>
		</noscript>
		<ul id="navbar">
			<?php 
			if ($article != '') {
                $category = $article->getCategoryCat();
			} else if ($_GET['cat'] != '') {
				$category = $_GET['cat'];
			}
				
            $sql = "SELECT label,cat FROM `category` 
                WHERE hidden=0 
                AND id>0 
                ORDER BY id ASC";
            $cats = $db->get_results($sql);
            foreach($cats as $key => $cat) { ?>
                <li class="<?php echo $cat->cat; ?> <?php if($category==$cat->cat) echo 'selected'; ?> <?php if($cat->cat == 'news') echo 'first'; ?> <?php if($cat->cat == 'sport') echo 'last'; ?>">
                    <a href="<?php echo STANDARD_URL.$cat->cat; ?>/">
                        <?php echo $cat->label; ?>
                    </a>
                </li>
            <?php } ?>	
		</ul>
		<script type="text/javascript">
			document.getElementById('navbar').style.display = 'block';
		</script>
	</div>
	<div class="clear"></div>
</div>
<!-- End of Navigation -->