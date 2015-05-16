<?php
namespace application\protect\modules\manage\controllers;

class IndexController extends \application\protect\controllers\CommonController
{
	public $layout = 'manage';
	
	public function actionIndex()
	{
		$db = \framework\component\Db::getInstance();
		$html = $this->render('index', array('db' => $db));
		echo $html;
	}
}