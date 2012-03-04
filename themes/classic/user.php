<?php
$timing->log('user page');

$meta = '
    <meta property="og:title" content="'.$user->getName().'"/>
    <meta property="og:image" content="http://felixonline.co.uk/img/title.jpg"/>
    <meta property="og:url" content="'.$user->getURL().'"/>
    <meta property="og:type" content="profile"/>
    <meta property="og:locale" content="en_GB"/>
    <meta property="og:description" content="'.$user->getDescription().'"/>
';

$header = array(
    'title' => $user->getName().' - '.'Felix Online',
    'meta' => $meta
);

$theme->render('header', $header);
?>
<div class="container_12 usercontainer">
    <!-- Sidebar -->
    <div class="sidebar grid_4 push_8">
        <?php if ($articles > 2) { ?>
            <div id="userPopular">
                <h3>Most Popular Articles</h3>
                <ol>
                <?php foreach(get_articles_by_user_popular($user) as $article) { ?>
                    <li id="userPopList">
                        <div id="popTitle">
                            <?php if (is_logged_in() == $user) { ?>
                            <div id="popHits">
                                <?php echo get_article_hits($article); ?> hits
                            </div>
                            <?php } ?>
                            <a href="<?php echo article_url($article);?>"><?php echo get_article_title($article);?></a>
                        </div>
                    </li>
                <?php } ?>
                </ol>
            </div>
        <?php } ?>
        <?php if ($comments) { ?>
            <div id="recentComments">
                <h3>Recent Comments</h3>
                <?php 
                if ($user_comment_popularity = get_user_comment_popularity($user)) {
                    list($dislikes,$likes) = $user_comment_popularity;
                    $ratings = $dislikes + $likes;
                    $popularity = round(100 * $likes / $ratings);
                }
                if ($ratings) { ?>
                    <span id="popularity">(Popularity: <?php echo $popularity;?>% over <?php echo $ratings;?> ratings)</span>
                <?php } ?>
                <ul id="commentList">
                    <?php foreach (get_article_comments_by_user($user) as $comment) {
                        echo '<li><a href="'.article_url($comment[0]).'">'.get_article_title($comment[0]).'</a> <p>"'.trim_text($comment[2], 130).'"</p></li>';
                    } ?>
                </ul>
                
                <?php // if number of comments is greater than NUMBER_OF_POPULAR_COMMENTS_USER then add link to view all comments ?>
            </div>
        <?php } ?>
        <?php 
            $theme->render('sidebar/fbActivity');
            $theme->render('sidebar/mostPopular');
            //$theme->render('sidebar/mediaBox');
        ?>
    </div>
    <!-- End of sidebar -->
    <div class="grid_8 pull_4 user omega clearfix">
        <div id="userInfoCont" class="clearfix">
			<form id="profileform">
                <h2>
                    <?php echo $user->getName(); ?><span>
                        <?php if ($currentuser->getUser() == $user) { ?>
                            <a href="#" id="editProfile">Edit Profile</a>
                            <a href="#" id="editProfileSave" style="display:none;">Save Profile</a>
                        <?php } ?>
                    </span>
                    <span class="loading">Saving...</span>
                </h2>
				<ul id="userInfo">
					<li><?php //echo $info[0]; ?></li>
					<li><?php //echo $info[1]; ?></li>
				</ul>
                <div id="personalCont" class="clearfix">
                    <div id="personalCont">
                        <?php if($user->getDescription()) {
                            echo $description;
                        } else if($currentuser->getUser() == $user) {
                            echo "Add some personal info....";
                        } ?>
                    </div>
                    <div id="personalLinks">
                        <ul>
                            <?php if($user->getFacebook()) { ?>
                                <li class="facebook">
                                    <a href="<?php echo $user->getFacebook(); ?>" target="_blank">Facebook</a>
                                </li>
                            <?php } ?>
                            <?php if($user->getTwitter()) { ?>
                                <li class="twitter">
                                    <a href="http://www.twitter.com/<?php echo $user->getTwitter(); ?>" target="_blank">@<?php echo $user->getTwitter(); ?></a>
                                </li>
                            <?php } ?>
                            <?php if($user->getEmail()) { ?>
                                <li class="useremail">
                                    <?php echo Utility::hideEmail($user->getEmail()); ?>
                                </li>
                            <?php } ?>
                            <?php if($user->getWebsiteurl()) { ?>
                                <li class="website">
                                    <a href="<?php echo $user->getWebsiteurl();?>" target="_blank">
                                        <?php echo $user->getWebsitename();?>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </form>
            <?php //if ($articles) { ?>
                <!-- Articles -->
                <div id="articleListCont">
                    <h3 id="userArticleTitle">
                        Articles <span>
                            <a href="rss.php?id=<?php echo $user->getUser();?>" target="_blank" id="userRSS">RSS Feed</a>
                        </span>
                    </h3>
                    <?php 
                    if ($pagenum == 1) {
                        $articles = $user->getArticles(1);
                        foreach($articles as $key => $object) {
                            $article = new Article($object->id);
                            if ($key < 4) { ?>
                                <div class="userArticle clearfix">
                                    <div class="userArticleDate grid_1 alpha">
                                        <span>
                                            <?php echo date('jS', $article->getDate()); ?>
                                        </span>
                                        <br/>
                                        <?php echo date('F Y',$article->getDate()); ?>
                                        <br/>
                                        <?php if ($currentuser->getUser() == $user->getUser()) { ?>
                                            <div><?php echo $article->getHits(); ?> hits</div>
                                        <?php } ?>
                                    </div>
                                    <?php 
                                            $scale = $size[0]/220;
                                            if ($check > 200) 
                                    ?>
                                    <div class="userArticleInfo grid_7 omega clearfix <?php if($article->getCategoryCat() == 'comment' || !$image = $article->getImage()) echo 'second';?>">
                                        <h3>
                                            <a href="<?php echo $article->getURL();?>">
                                                <?php echo $article->getTitle();?>
                                            </a>
                                        </h3>
                                        <div class="subHeader <?php if($image && $image->isTall()) echo 'wide';?>" >
                                            <p>
                                                <?php echo $article->getPreview(30); ?>
                                            </p>
                                            <div id="storyMeta">
                                                <ul class="metaList">
                                                    <li id="category">
                                                        <a href="<?php echo $article->getCategory()->getURL();?>" class="<?php echo $article->getCategoryCat();?>">
                                                            <?php echo $article->getCategoryLabel();?>
                                                        </a>
                                                    </li>
                                                    <?php if($article->getNumComments()) { ?>
                                                        <li id="comments">
                                                            <a href="<?php echo $article->getURL();?>#commentHeader">
                                                                <?php echo $article->getNumComments().' comment'.($article->getNumComments() != 1 ? 's' : '');?>
                                                            </a>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                        </div>
                                        <?php if($article->getCategoryCat() != 'comment') { 
                                                if($article->getImage()) { ?>
                                                    <div id="secondStoryPic">
                                                        <a href="<?php echo $article->getURL();?>">
                                                            <?php if($article->getImage()->isTall(220, 220)) { ?>
                                                                <img id="secondStoryPhoto" alt="<?php echo $article->getImage()->getTitle();?>" src="<?php echo $article->getImage()->getURL(120, 155); ?>">
                                                            <?php } else { ?>
                                                                <img id="secondStoryPhoto" alt="<?php echo $article->getImage()->getTitle();?>" src="<?php echo $article->getImage()->getURL(220, 150); ?>">
                                                            <?php } ?>
                                                        </a>
                                                    </div>
                                        <?php   } 
                                            } ?>
                                    </div>
                                </div>
                    <?php   } else { ?>
                                <div class="userArticle clearfix">
                                    <div class="userArticleDate grid_1 alpha">
                                        <span><?php echo date('jS',$article->getDate()); ?></span><br/>
                                        <?php echo date('F Y',$article->getDate()); ?>
                                        <?php if ($currentuser->getUser() == $user->getUser()) { ?>
                                            <div><?php echo $article->getHits(); ?> hits</div>
                                        <?php } ?>
                                    </div>
                                    <div class="userArticleInfo grid_7 omega second clearfix">
                                        <h3>
                                            <a href="<?php echo $article->getURL();?>">
                                                <?php echo $article->getTitle();?>
                                            </a>
                                        </h3>
                                        <div class="subHeader">
                                            <p>
                                                <?php echo $article->getPreview(30); ?>
                                            </p>
                                            <div id="storyMeta">
                                                <ul class="metaList">
                                                    <li id="category">
                                                        <a href="<?php echo $article->getCategory()->getURL();?>" class="<?php echo $article->getCategory()->getCat();?>">
                                                            <?php echo $article->getCategory()->getLabel();?>
                                                        </a>
                                                    </li>
                                                    <?php if($article->getNumComments()) { ?>
                                                        <li id="comments">
                                                            <a href="<?php echo $article->getURL();?>#commentHeader">
                                                                <?php echo $article->getNumComments().' comment'.($article->getNumComments() != 1 ? 's' : '');?>
                                                            </a>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>	
                    <?php   } 
                        } 
                    } else { 
                        $articles = $user->getArticles($pagenum);
                        foreach($articles as $key => $object) { 
                            $article = new Article($object->id); ?>
                                <div class="userArticle clearfix">
                                    <div class="userArticleDate grid_1 alpha">
                                        <span><?php echo date('jS',$article->getDate()); ?></span><br/>
                                        <?php echo date('F Y',$article->getDate()); ?>
                                        <?php if ($currentuser->getUser() == $user->getUser()) { ?>
                                            <div><?php echo $article->getHits(); ?> hits</div>
                                        <?php } ?>
                                    </div>
                                    <div class="userArticleInfo grid_7 omega second clearfix">
                                        <h3>
                                            <a href="<?php echo $article->getURL();?>">
                                                <?php echo $article->getTitle();?>
                                            </a>
                                        </h3>
                                        <div class="subHeader">
                                            <p>
                                                <?php echo $article->getPreview(30); ?>
                                            </p>
                                            <div id="storyMeta">
                                                <ul class="metaList">
                                                    <li id="category">
                                                        <a href="<?php echo $article->getCategory()->getURL();?>" class="<?php echo $article->getCategory()->getCat();?>">
                                                            <?php echo $article->getCategory()->getLabel();?>
                                                        </a>
                                                    </li>
                                                    <?php if($article->getNumComments()) { ?>
                                                        <li id="comments">
                                                            <a href="<?php echo $article->getURL();?>#commentHeader">
                                                                <?php echo $article->getNumComments().' comment'.($article->getNumComments() != 1 ? 's' : '');?>
                                                            </a>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>	
                    <?php } 
                    } ?>
                </div>
                <!-- End of articles -->
                
                <!-- Page list -->
                <div class="grid_8 clearfix">
                    <ul id="pageList" class="clearfix">
                        <li id="desc">Pages:</li>
                        <?php if ($pagenum != 1) { // Previous page arrow ?>
                            <li class="arrow">
                                <a href="<?php echo $user->getURL($pagenum-1); ?>">
                                    &#171;
                                </a>
                            </li>
                        <?php } 
                            $pages = $user->getNumPages();
                            if ($pages > 1) {
                                $span = NUMBER_OF_PAGES_IN_PAGE_LIST;
                                if ($pages > $span) { // more pages than limit
                                    if ($pagenum >= ($span/2)) {
                                        $start = ($pagenum - $span/2)+1;
                                        $limit = $pagenum + $span/2;
                                        if ($limit > $pages) {
                                            $limit = $pages;
                                            $start = $limit - $span;
                                        }
                                    } else {
                                        $start = 1;
                                        $limit = $span;
                                    }
                                } else {
                                    $limit = $pages;
                                    $start = 1;
                                }
                                for ($i=$start;$i<=$limit;$i++) {
                                    if($pagenum==$i) { ?>
                                        <li class="selected">
                                    <?php } else { ?>
                                        <li>
                                            <a href="<?php echo $user->getURL($i); ?>">
                                    <?php } ?>
                                        <?php echo $i; ?>
                                    <?php if($pagenum==$i) { ?>
                                        </li>
                                    <?php } else { ?>
                                        </a></li>
                                    <?php }
                                }
                            } else { ?>
                                <li class="selected">1</li>
                            <?php }
                            if ($pagenum != $pages) { // Next page arrow ?>
                                <li class="arrow">
                                    <a href="<?php echo $user->getURL($pagenum+1);?>">
                                        &#187;
                                    </a>
                                </li>
                            <?php }
                        ?>
                    </ul>
                </div>
                <!-- End of page list -->
        </div>
    </div>
</div>
<?php $timing->log('end of user page');?>
<?php $theme->render('footer'); ?>