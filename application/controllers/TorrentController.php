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
		echo $this->render('upload');
	}
}