<?php
class RuleController extends CommonController
{
	public $layout = 'manage';
	
	public function actionList()
	{
		$model = RuleModel::model();
		$ruleList = $model->order('sort ASC,id DESC')->select();
		$html = $this->render('rulelist', array('ruleList' => $ruleList));
		echo $html;
	}
	
	public function actionEdit()
	{
		$model = RuleModel::model();
		if (App::ins()->request->isGet())
		{
			if (empty($_GET['id']) || !ctype_digit($_GET['id']))
			{
				$this->goError();
			}
			$rule = $model->findByPk($_GET['id']);
			$model->setData($rule);
			$html = $this->render('ruleform', array('model' => $model));
			echo $html;
		}
		elseif (App::ins()->request->isPost())
		{
			$this->submit($model, $_POST, 'update');
		}
	}
	
	public function actionAdd()
	{
		$model = RuleModel::model();
		if (App::ins()->request->isGet())
		{
			$html = $this->render('ruleform', array('model' => $model));
			echo $html;
		}
		elseif (App::ins()->request->isPost())
		{
			$this->submit($model, $_POST, 'insert');
		}
		
	}
	
	private function submit(Model $model, array $data, $action = 'insert')
	{
		if ($model->validate($data))
		{
			if ($action === 'insert')
			{
				$result = $model->insert($data);
			}
			elseif ($action === 'update')
			{
				$result = $model->updateByPk($data['id'], $data);
			}
			if ($result >= 0)
			{
				$this->redirect('manage/rule/list');
			}
		}
		$model->setData($data);
		$html = $this->render('ruleform', array('model' => $model));
		echo $html;
	}
}