<?php
class UserController extends Controller
{
	public $layout = 'manage';
	
	public function actionUserlist()
	{
		$html = $this->render('userlist');
		echo $html;
	}
	
	public function actionUseradd()
	{
		if (App::ins()->request->isGet())
		{
			$html = $this->render('userform');
			echo $html;
		}
		elseif(App::ins()->request->isPost())
		{
			echo '提交添加section';
		}
	}
}