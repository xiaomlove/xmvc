<?php
class RoleController extends Controller
{
	public $layout = 'manage';
	
	public function actionRolelist()
	{
		$html = $this->render('rolelist');
		echo $html;
	}
	
	public function actionRoleadd()
	{
		$model = RoleModel::model();
		if (App::ins()->request->isGet())
		{
			$html = $this->render('roleform', array('model' => $model));
			echo $html;
		}
		elseif (App::ins()->request->isPost())
		{
			if ($model->validate($_POST))
			{
				$timeLimitStr = '';
				switch ($_POST['unit'])
				{
					case 'week':
						$timeLimitStr = $_POST['register_time_limit'].'周';
						$_POST['register_time_limit'] *= 3600*24*7;
						break;
					case 'month':
						$timeLimitStr = $_POST['register_time_limit'].'月';
						$_POST['register_time_limit'] *= 3600*24*30;
						break;
					case 'year':
						$timeLimitStr = $_POST['register_time_limit'].'年';
						$_POST['register_time_limit'] *= 3600*24*365;
						break;
				}
				unset($_POST['unit']);
				$_POST['register_time_limit_string'] = $timeLimitStr;
				$result = $model->insert($_POST);
				if (empty($result))
				{
					$model->setError('name', '未知错误，添加失败！');
				}
				else 
				{
					$this->redirect('manage/role/rolelist');
				}
			}
			$model->setData($_POST);
			$html = $this->render('roleform', array('model' => $model));
			echo $html;
		}
	}
}