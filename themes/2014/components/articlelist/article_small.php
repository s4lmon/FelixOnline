<!-- Secondary story -->
				<div class="felix-front-news-secondary">
					<div class="row">
						<div class="small-5 columns felix-front-news-secondary-img">
						<?php if ($image = $article->getImage()) { ?>
								<a href="<?php echo $article->getURL();?>">
								<?php if($image->isTall(800, 800)) { ?>
									<img alt="<?php echo $image->getTitle();?>" src="<?php echo $image->getURL(500, 600);?>">
								<?php } else { ?>
									<img alt="<?php echo $image->getTitle();?>" src="<?php echo $image->getURL(800, 600);?>">
								<?php } ?>
								</a>
						<?php } else { ?>
								<a href="<?php echo $article->getURL();?>">
									<img alt="" src="<?php echo IMAGE_URL.'800/600/'.DEFAULT_IMG_URI; ?>">
								</a>
						<?php } ?>
						</div>
						<div class="small-7 columns felix-front-news-secondary-text">
							<span class="felix-front-news-date"><?php echo date("l F j, Y",$article->getPublished());?></span>
							<h3><a href="<?php echo $article->getURL();?>"><?php echo $article->getTitle();?></a></h3>
						</div>
					</div>
				</div>
<!-- End of secondary story -->