<?php
class TorrentController extends CommonController
{
	public $layout = 'tinypt';
	
	public function actionList()
	{
		$this->setPageTitle('种子列表');
		echo $this->render('torrent');
	}
	
	public function actionDetail()
	{
		$this->setPageTitle('种子详情');
		echo $this->render('detail');
	}
	
	public function actionUpload()
	{
		$this->setPageTitle('发布种子');
		if(App::ins()->request->isPost())
		{
			echo '<pre/>';
			var_dump($_POST);
			var_dump($_FILES);
			echo '<hr/>';
			$model = TorrentModel::model();
			if($model->validate($_POST))
			{
				echo '验证成功！';
			}
			else 
			{
				var_dump($model->getErrors());
			}
			exit;
		}
		else
		{
			
		}
		echo $this->render('upload');
	}
}