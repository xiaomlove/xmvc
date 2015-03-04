<?php
class ForumController extends Controller
{
	public $layout = 'tinypt';
	
	public function actionSection()
	{
		$html = $this->render('forum');
		echo $html;
	}
}