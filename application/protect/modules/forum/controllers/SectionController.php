<?php
namespace application\protect\modules\forum\controllers;

//use framework\App as App;
use application\protect\models as models;

class SectionController extends \application\protect\controllers\CommonController
{
	public $layout = 'tinypt';
	
	public function init()
	{
		$this->breadcrumbs[] = array('name' => 'TinyHD论坛', 'url' => $this->createUrl('forum/section/list'));
	}
	public function actionList()
	{
		$model = models\ForumsectionModel::model();
		$sql = "SELECT b.* FROM forum_section a INNER JOIN forum_section b ON b.parent_id=a.id OR b.id=a.id WHERE a.parent_id=0 ORDER BY a.sort ASC,b.sort ASC";
		$sectionList = $model->findBySql($sql);
		$html = $this->render('forum', array('sectionList' => $sectionList));
		echo $html;
	}
	
	public function actionView()
	{
		
	}
}