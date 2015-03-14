<?php
class OptionController extends CommonController
{
	public $layout = 'manage';
	
	public function actionForumset()
	{
		$this->setPageTitle('分页设置');
		$a = OptionModel::model()->get('forum_reply_pagination');
		$html = $this->render('forumset');
		echo $html;
	}
}