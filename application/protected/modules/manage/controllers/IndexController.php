<?php
class IndexController extends Controller
{
	public $layout = 'manage';
	
	public function actionIndex()
	{
		$db = Db::getInstance();
		$html = $this->render('index', array('db' => $db));
		echo $html;
	}
}