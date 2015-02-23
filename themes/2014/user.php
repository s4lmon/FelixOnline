<?php

$meta = '
	<meta property="og:title" content="'.$user->getName().'"/>
	<meta property="og:image" content="http://felixonline.co.uk/img/title.jpg"/>
	<meta property="og:url" content="'.$user->getURL().'"/>
	<meta property="og:type" content="profile"/>
	<meta property="og:locale" content="en_GB"/>
	<meta property="og:description" content="'.$user->getDescription().'"/>
';
if($user->hasArticlesHiddenFromRobots() && $user->getUser!="felix" ) {
	$meta .= '<meta name="robots" content="noindex"/>';
}
$header = array(
	'title' => $user->getName().' - '.'Felix Online',
	'meta' => $meta
);

$theme->render('components/header', $header);

$theme->render('components/noticeBlock', array('no_frontpage_only' => true));

?>
		<div class="user-title">
			<div class="row">
				<div class="medium-6 columns">
					<h1><?php echo $user->getName(); ?></h1>
				</div>
				<div class="medium-6 columns">
					<div class="user-meta text-right show-for-medium-up">
					<?php if ($articles || $comments) { ?>
						<?php if ($articles){ ?>
						Since <b><?php echo date('d/m/Y',$user->getFirstLogin());?></b>, <?php echo $user->getFirstName(); ?> has written:<br>
						<?php echo '<b>'.$article_count; ?></b> article<?php echo ($article_count != 1 ? 's' : '');?>
						<?php } ?>
					<?php } ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="small-12 columns">
				<div class="section-bar section-generic"></div>
			</div>
		</div>
	<div class="row">
		<div class="medium-8 columns">
			<?php if ($currentuser->getUser() == $user->getUser()): ?>
				<?php
					$theme->render('components/editProfile', array('user' => $user));
				?>
			<?php endif; ?>
	<!-- End of sidebar -->
		<?php if (!empty($articles)) { ?>
			<!-- Articles -->
				<?php foreach($articles as $key => $article) {
					$theme->render('components/articlelist/article_medium', array(
						'article' => $article,
						'show_authors' => true
					));
				} ?> 
			<!-- End of articles -->
			
			<!-- Page list -->
			<?php $theme->render('components/pagination', array(
				'pagenum' => $pagenum,
				'class' => $user,
				'pages' => $pages,
				'span' => ARTICLES_PER_USER_PAGE
			)); ?>
			<!-- End of page list -->
		<?php } ?>
	</div>
	<div class="medium-4 columns">
		<?php if($user->getDescription()) { ?>
		<div class="felix-item-title felix-item-title felix-item-title-generic">
			<h3>About <?php echo $user->getFirstName(); ?></h3>
		</div>
		<br>
		<p><?php echo nl2br($user->getDescription()); ?></p>
		<?php } ?>
		<div class="felix-item-title felix-item-title felix-item-title-generic">
			<h3>Contact <?php echo $user->getFirstName(); ?></h3>
		</div>
		<div class="felix-contact-area">
			<?php if(!$user->getShowEmail() && !$user->getFacebook() && !$user->getTwitter() && !($user->getWebsitename() && $user->getWebsiteurl())): ?>
			<p>Sorry, there are no contact details available.</p>
			<?php endif; ?>
			<?php if($user->getShowEmail()): ?>
			<div class="row felix-contact-row">
				<div class="small-3 columns">
					<a href="mailto:<?php echo $user->getEmail(); ?>"><img src="<?php echo STANDARD_URL.'themes/'.THEME_NAME.'/'; ?>img/email.png"></a>
				</div>
				<div class="small-9 columns">
					<p><a href="mailto:<?php echo $user->getEmail(); ?>"><?php echo $user->getEmail(); ?></a></p>
				</div>
			</div>
			<?php endif; ?>
			<?php if($user->getFacebook()): ?>
			<div class="row felix-contact-row">
				<div class="small-3 columns">
					<a href="http://facebook.com/<?php echo $user->getFacebook(); ?>"><img src="<?php echo STANDARD_URL.'themes/'.THEME_NAME.'/'; ?>img/fb.png"></a>
				</div>
				<div class="small-9 columns">
					<p><a href="<?php echo $user->getFacebook(); ?>">Facebook</a></p>
				</div>
			</div>
			<?php endif; ?>
			<?php if($user->getTwitter()): ?>
			<div class="row felix-contact-row">
				<div class="small-3 columns">
					<a href="http://twitter.com/<?php echo $user->getTwitter(); ?>"><img src="<?php echo STANDARD_URL.'themes/'.THEME_NAME.'/'; ?>img/twitter.png"></a>
				</div>
				<div class="small-9 columns">
					<p><a href="http://twitter.com/<?php echo $user->getTwitter(); ?>">@<?php echo $user->getTwitter(); ?></a></p>
				</div>
			</div>
			<?php endif; ?>
			<?php if($user->getWebsitename() && $user->getWebsiteurl()): ?>
			<div class="row felix-contact-row">
				<div class="small-3 columns">
					<a href="<?php echo $user->getWebsiteurl(); ?>"><img src="<?php echo STANDARD_URL.'themes/'.THEME_NAME.'/'; ?>img/web.png"></a>
				</div>
				<div class="small-9 columns">
					<p><a href="<?php echo $user->getWebsiteurl(); ?>"><?php echo $user->getWebsitename(); ?></a></p>
				</div>
			</div>
			<?php endif; ?>
			<?php if ($currentuser->getUser() == $user->getUser()): ?>
				<center><a href="#" data-reveal-id="editProfileModal" class="button small">Update your details</a></center>
			<?php endif; ?>
		</div>

		<?php if ($article_count > 2 && $popular_articles) { ?>
			<div class="felix-item-title felix-item-title felix-item-title-generic">
				<h3>Most Popular Articles</h3>
			</div>
			<ol class="user-popular">
			<?php foreach($popular_articles as $article) { ?>
				<li class="user-popular-item">
					<div class="popular-item-title">
						<?php if($currentuser->isLoggedIn() == $user->getUser()) { ?>
						<div class="popular-item-hits">
							<?php echo $article->getHits(); ?> hits
						</div>
						<?php } ?>
						<a href="<?php echo $article->getURL();?>"><?php echo $article->getTitle();?></a>
					</div>
				</li>
			<?php } ?>
			</ol>
		<?php } ?>
		<?php if ($categories) { ?>
			<div class="felix-item-title felix-item-title felix-item-title-generic">
				<h3><?php echo $user->getFirstName(); ?> Edits</h3>
			</div>
			<ul class="user-popular">
			<?php foreach($categories as $category) { ?>
				<li class="user-popular-item">
					<a href="<?php echo STANDARD_URL.$category->getCat(); ?>/">
						<b><?php echo $category->getLabel(); ?></b>
					</a>
				</li>
			<?php } ?>
			</ul>
			<p>Contact <?php echo $user->getFirstName(); ?> if you would like to get involved in any of these sections of <i>Felix</i> - new writers always welcome!</p>
		<?php } ?>
	</div>
</div>
<?php $theme->render('components/footer'); ?>
