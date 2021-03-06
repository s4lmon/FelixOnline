<?php
/*
 * Felix Online
 */

use FelixOnline\Core;

ob_start();

try {
	if(file_exists('.maintenance')) {
		require('errors/offline.php');
		exit;
	}

	require_once('bootstrap.php');

	/* If the url is on the union servers then redirect to custom url */
	/*
	if (strstr($_SERVER['HTTP_HOST'],"union.ic.ac.uk") !== false) {
		header("Location: ".STANDARD_URL.substr($_SERVER['REQUEST_URI'],(1+strrpos($_SERVER['REQUEST_URI'],"/"))));
	}
	 */

	$hooks = new Core\Hooks();

	/*
	 * Routes
	 */
	$urls = array(
		'/' => 'FrontpageController',
		'/user/(?P<user>[a-zA-Z0-9_-]+)' => 'UserController',
		'/user/(?P<user>[a-zA-Z0-9_-]+)/(?P<page>[0-9]+)' => 'UserController',
		'/search/(.*)' => 'SearchController',
		'/topic' => 'TopicController',
		'/topic/(?P<slug>[a-zA-Z0-9]+)' => 'TopicController',
		'/topic/(?P<slug>[a-zA-Z0-9]+)/(?P<page>[0-9]+)' => 'TopicController',
		'/(?P<cat>[a-zA-Z]+)' => 'CategoryController',
		'/(?P<cat>[a-zA-Z]+)/(?P<page>[0-9]+)' => 'CategoryController',
		'/(?P<cat>[a-zA-Z]+)/(?P<id>[0-9]+)/(?P<title>[a-zA-Z0-9_-]+)/.*' => 'ArticleController',
		'/(?P<login>(login))/.*' => 'AuthController',
		'/(?P<logout>(logout))/.*' => 'AuthController',
		'/auth/.*' => 'AuthController',
		'/issuearchive/decade/(?P<decade>[0-9]+)' => 'ArchiveController',
		'/issuearchive/year/(?P<year>[0-9]+)' => 'ArchiveController',
		'/issuearchive/issue/(?P<id>[0-9]+)' => 'ArchiveController',
		'/issuearchive/issue/(?P<id>[0-9]+)/(?P<download>download)' => 'ArchiveController',
		'/issuearchive/issue/(?P<id>[0-9]+)/(?P<download>download/(?P<part>[a-zA-Z]+))' => 'ArchiveController',
		'/issuearchive/.*' => 'ArchiveController',
		'/rss' => 'RSSController',
		'/rss/(?P<cat>[a-zA-Z]+)' => 'RSSController',
		'/rss/user/(?P<user>[a-zA-Z0-9]+)' => 'RSSController',
		'/go/(?P<link>[a-zA-Z0-9]+)' => 'LinkController',
		'/redirect/(?P<advert>[0-9]+)' => 'AdvertController',
		'/validate/(?P<code>[a-zA-Z0-9]+)' => 'ValidationController',
	);

	/*
	 * Add pages to routes
 */
	try {
		$pages = (new \FelixOnline\Core\PageManager())
			->all();

		foreach($pages as $page) {
			$urls['/'.$page->getSlug()] = 'pageController';
		}
	} catch (Exceptions\InternalException $e) {

	}
} catch (Exception $e) {
	$prior_exception = null;
	require('errors/error.php');
	exit();
}

/*
 * Include Controllers
 */
require_once(BASE_DIRECTORY.'/controllers/baseController.php');
foreach (glob(BASE_DIRECTORY.'/controllers/*.php') as $filename) {
	require_once($filename);
}

// set X-Robots-Tag if not on live server
if (PRODUCTION_FLAG == false) {
	header("X-Robots-Tag: noindex, nofollow, noarchive");
}

try {
	// try mapping request to urls
	if((isset($_POST['username']) && isset($_POST['password'])) &&
		strstr(Utility::currentPageURL(), AUTHENTICATION_PATH) != false
	) { // if request is to auth path
		$relpath = substr(
			substr(
				AUTHENTICATION_PATH,
				strpos(AUTHENTICATION_PATH, AUTHENTICATION_SERVER)
				+ strlen(AUTHENTICATION_SERVER)
			), 0, -1);
		glue::stick($urls, $relpath);
	} else if(defined('RELATIVE_PATH')) { // if a relative path is defined
		glue::stick($urls, RELATIVE_PATH);
	} else {
		glue::stick($urls);
	}

} catch (Exception $e) {
	if ($e instanceof NotFoundException) {
		// If any exception which amounts to something not being found
		$prior_exception = $e;
		try {
			// First attempt to show nice 404 page. Throw away current theme data
			ob_end_clean();
			ob_start();
			$controller = new NotFoundController();
			$controller->GET(array($prior_exception));
			ob_end_flush();
			// End execution
			exit();
		} catch (Exception $e) {

			// there is an exception in the above code - time to bail out and display the emergency error page
			ob_end_clean();
			ob_start();
			require('errors/error.php');
			ob_end_flush();
			// End execution
			exit();
		}
	} else {
		// If something bad happened
		// time to bail out and display the emergency error page
		ob_end_clean();
		ob_start();
		require('errors/error.php');
		ob_end_flush();
		// End execution
		exit();
	}
}

ob_end_flush();
