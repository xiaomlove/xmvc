<?php
class SectionController extends Controller
{
	public $layout = 'tinypt';
	
	public function actionList()
	{
		$html = $this->render('forum');
		echo $html;
	}
}