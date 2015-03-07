<?php
class RoleController extends CommentController
{
	public $layout = 'manage';
	
	public function actionRolelist()
	{
		$model = RoleModel::model();
		$roleList = $model->order('level ASC')->select();
//		var_dump($roleList);exit;
		$html = $this->render('rolelist', array('roleList' => $roleList));
		echo $html;
	}
	
	public function actionRoleadd()
	{
		$model = RoleModel::model();
		$action = $this->createUrl('manage/role/roleadd');
		if (App::ins()->request->isGet())
		{
			$html = $this->render('roleform', array('model' => $model, 'action' => $action));
			echo $html;
		}
		elseif (App::ins()->request->isPost())
		{
			$model->scene = 'add';
			self::submit($model, $action);
		}
	}
	
	public function actionRoleedit()
	{
		$model = RoleModel::model();
		if (App::ins()->request->isGet())
		{
			if (empty($_GET['id']) || !ctype_digit(strval($_GET['id'])))
			{
				$this->goError();
			}
			$action = $this->createUrl('manage/role/roleedit', array('id' => $_GET['id']));
			$role = $model->findByPk($_GET['id']);
			$model->setData($role);//直接赋值，通过getData取出来，重用roleform。
			$html = $this->render('roleform', array('model' => $model, 'action' => $action));
			echo $html;
		}
		else
		{
			//提交更改操作
			$action = $this->createUrl('manage/role/roleedit', array('id' => $_POST['id']));
			self::submit($model, $action);
		}
		
	}
	
	public function submit(&$model, $action)
	{
		if ($model->validate($_POST))
		{
			switch ($_POST['register_time_limit_unit'])
			{
				case 'week':
					$_POST['register_time_limit'] = $_POST['register_time_limit_value']*3600*24*7;
					break;
				case 'month':
					$_POST['register_time_limit'] = $_POST['register_time_limit_value']*3600*24*30;
					break;
				case 'year':
					$_POST['register_time_limit'] = $_POST['register_time_limit_value']*3600*24*365;
					break;
			}
			
			//分享率不能小于上传量/下载量
			if ($_POST['uploaded_limit']/$_POST['downloaded_limit'] > $_POST['ratio_limit'])
			{
				$model->setError('ratio_limit', '分享率不能小于上传/下载');
				goto A;
			}
			//检验等级数值是否跟其他数值相对应，也就是一个更高的等级不能有更低的其他数值
			
			$noError = TRUE;
			
			//更高级别，我的不能比它大
			$highLevel = $model->where('level>'.$_POST['level'])->order('level ASC')->limit(1)->select();
			if (!empty($highLevel))
			{
				$highLevel = $highLevel[0];
				if ($_POST['bonus_limit'] > $highLevel['bonus_limit'])
				{
					$model->setError('bonus_limit', '不能比更高等级的角色的魔力('.$highLevel['bonus_limit'].')还高');
					$noError = FALSE;
				}
				if ($_POST['downloaded_limit'] > $highLevel['downloaded_limit'])
				{
					$model->setError('downloaded_limit', '不能比更高等级的角色的下载量('.$highLevel['downloaded_limit'].')还高');
					$noError = FALSE;
				}
				if ($_POST['uploaded_limit'] > $highLevel['uploaded_limit'])
				{
					$model->setError('uploaded_limit', '不能比更高等级的角色的上传量('.$highLevel['uploaded_limit'].')还高');
					$noError = FALSE;
				}
				if ($_POST['ratio_limit'] > $highLevel['ratio_limit'])
				{
					$model->setError('ratio_limit', '不能比更高等级的角色的分享率('.number_format($highLevel['ratio_limit'], 2, '.', '').')还高');
					$noError = FALSE;
				}
				if ($_POST['register_time_limit'] > $highLevel['register_time_limit'])
				{
					$model->setError('register_time_limit_value', '不能比更高等级的角色的注册时间('.$highLevel['register_time_limit_value'].self::getUnit($highLevel['register_time_limit_unit']).')还高');
					$noError = FALSE;
				}
				
			}
			
			if (!$noError)
			{
				goto A;
			}
			
			//更低等级，我的不能比它小
			$lowLevel = $model->where('level<'.$_POST['level'])->order('level DESC')->limit(1)->select();
			if (!empty($lowLevel))
			{
				$lowLevel = $lowLevel[0];
				if ($_POST['bonus_limit'] < $lowLevel['bonus_limit'])
				{
					$model->setError('bonus_limit', '不能比更低等级的角色的魔力('.$lowLevel['bonus_limit'].')还低');
					$noError = FALSE;
				}
				if ($_POST['downloaded_limit'] < $lowLevel['downloaded_limit'])
				{
					$model->setError('downloaded_limit', '不能比更低等级的角色的下载量('.$lowLevel['downloaded_limit'].')还低');
					$noError = FALSE;
				}
				if ($_POST['uploaded_limit'] < $lowLevel['uploaded_limit'])
				{
					$model->setError('uploaded_limit', '不能比更低等级的角色的上传量('.$lowLevel['uploaded_limit'].')还低');
					$noError = FALSE;
				}
				if ($_POST['ratio_limit'] < $lowLevel['ratio_limit'])
				{
					$model->setError('ratio_limit', '不能比更低等级的角色的分享率('.number_format($lowLevel['ratio_limit'], 2, '.', '').')还低');
					$noError = FALSE;
				}
				if ($_POST['register_time_limit'] < $lowLevel['register_time_limit'])
				{
					$model->setError('register_time_limit_value', '不能比更低等级的角色的注册时间('.$lowLevel['register_time_limit_value'].self::getUnit($lowLevel['register_time_limit_unit']).')还低');
					$noError = FALSE;
				}
				
			}
			if (!$noError)
			{
				goto A;
			}
			if (isset($_POST['id']))
			{
				$result = $model->updateByPk($_POST['id'], $_POST);
			}
			else 
			{
				$result = $model->insert($_POST);
			}
			
			if (empty($result) && $result != 0)
			{
				$model->setError('name', '未知错误，失败！');
			}
			else 
			{
				$this->redirect('manage/role/rolelist');
			}
		}
		A:
		$model->setData($_POST);
		$html = $this->render('roleform', array('model' => $model, 'action' => $action));
		echo $html;
	}
	
	public function getUnit(&$unit)
	{
		switch ($unit)
		{
			case 'week':
				$unit = '周';
				break;
			case 'month':
				$unit = '月';
				break;
			case 'year':
				$unit = '年';
				break;
		}
		return $unit;
	}
}