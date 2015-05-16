<?php
namespace application\protect\modules\manage\controllers;

use application\protect\models as models;

class OptionController extends \application\protect\controllers\CommonController
{
	public $layout = 'manage';
	
	public function actionForumset()
	{
		$this->setPageTitle('分页设置');
		$a = models\OptionModel::model()->get('forum_reply_pagination');
		$html = $this->render('forumset');
		echo $html;
	}
}