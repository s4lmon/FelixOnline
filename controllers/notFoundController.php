<?php
/**
 * Not found controller
 */
class NotFoundController extends BaseController
{
	function GET($matches)
	{
		header("HTTP/1.0 404 Not Found");
		$this->theme->appendData(array('e' => $matches[0]));
		$this->theme->render('404_page');
	}
}

