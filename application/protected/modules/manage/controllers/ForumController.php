<?php
class ForumController extends Controller
{
	public $layout = 'manage';
	
	public function actionSectionlist()
	{
		$html = $this->render('sectionlist');
		echo $html;
	}
	
	public function actionSectionadd()
	{
		if (App::ins()->request->isGet())
		{
			$html = $this->render('sectionform');
			echo $html;
		}
		elseif(App::ins()->request->isPost())
		{
			echo '提交添加section';
		}
	}
}