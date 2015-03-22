<?php
class RuleController extends CommonController
{
	public $layout = 'manage';
	
	public function actionList()
	{
		$model = RuleModel::model();
		$ruleList = $model->order('sort ASC')->select();
		$html = $this->render('rulelist', array('ruleList' => $ruleList));
		echo $html;
	}
	
	public function actionEdit()
	{
		if (empty($_GET['id']) || !ctype_digit($_GET['id']))
		{
			$this->goError();
		}
		$model = RuleModel::model();
		$rule = $model->findByPk($_GET['id']);
		$model->setData($rule);
		$html = $this->render('ruleform', array('model' => $model));
		echo $html;
	}
	
	public function actionAdd()
	{
		$model = RuleModel::model();
		$html = $this->render('ruleform', array('model' => $model));
		echo $html;
	}
}