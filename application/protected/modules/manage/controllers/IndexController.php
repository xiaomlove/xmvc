<?php
class IndexController extends Controller
{
	public $layout = 'manage';
	
	public function actionIndex()
	{
		$html = $this->render('index');
		echo $html;
	}
}