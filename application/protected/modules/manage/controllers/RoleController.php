<?php
class RoleController extends Controller
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
						$realTimeLimit = $_POST['register_time_limit']*3600*24*7;
						break;
					case 'month':
						$timeLimitStr = $_POST['register_time_limit'].'月';
						$realTimeLimit = $_POST['register_time_limit']*3600*24*30;
						break;
					case 'year':
						$timeLimitStr = $_POST['register_time_limit'].'年';
						$realTimeLimit = $_POST['register_time_limit']*3600*24*365;
						break;
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
					if ($realTimeLimit > $highLevel['register_time_limit'])
					{
						$model->setError('register_time_limit', '不能比更高等级的角色的注册时间('.$highLevel['register_time_limit_string'].')还高');
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
					if ($realTimeLimit < $lowLevel['register_time_limit'])
					{
						$model->setError('register_time_limit', '不能比更低等级的角色的注册时间('.$lowLevel['register_time_limit_string'].')还低');
						$noError = FALSE;
					}
					
				}
				if (!$noError)
				{
					goto A;
				}
				
				unset($_POST['unit']);
				$_POST['register_time_limit_string'] = $timeLimitStr;
				$_POST['register_time_limit'] = $realTimeLimit;
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
			A:
			$model->setData($_POST);
			$html = $this->render('roleform', array('model' => $model));
			echo $html;
		}
	}
}