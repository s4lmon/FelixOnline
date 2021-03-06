<?php
	// Get sub-categories
	$cats2 = (new \FelixOnline\Core\CategoryManager())
		->filter('hidden = 0')
		->filter('active = 1');

	if(!$currentuser->isLoggedIn()) {
		$cats2->filter('secret = 0');
	}

	$cats2 = $cats2->filter('id > 0')
		->filter('parent = %i', array($item->getId()))
		->order('order', 'ASC')
		->values();

	$active = false;

	if(isset($check) && $check == $item->getCat()) {
		$active = true;
	}

	foreach($parents as $parent) {
		if($parent->getCat() == $item->getCat()) {
			$active = true;
		}
	}
?>
	<li class="<?php if($active) echo 'active '; ?>">
		<a href="<?php echo $item->getURL(); ?>">
			<?php echo $item->getLabel(); ?>
		</a>
<?php
	if(!is_null($cats2)) {
		echo '<ul class="menu vertical">';
		foreach($cats2 as $sub_category) {
			$theme->render('components/navigation/nav_item', array('item' => $sub_category)); 
		}
		echo '</ul>';
	}
?>
	</li>