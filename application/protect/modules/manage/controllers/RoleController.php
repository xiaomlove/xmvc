<?php
namespace application\protect\modules\manage\controllers;

use framework\App;
use application\protect\models\RuleModel;
use application\protect\models\RoleModel;
use application\protect\models\RolegroupModel;


//角色  控制器

class RoleController extends \application\protect\controllers\CommonController
{
	public $layout = 'manage';
	
	private function _getRoleGroup()
	{
		if (App::ins()->request->isGet())
		{
			if (empty($_GET['group_id']) || !ctype_digit($_GET['group_id']))
			{
				$this->goError();exit;
			}
			$groupId = $_GET['group_id'];
		}
		elseif (App::ins()->request->isPost())
		{
			if (empty($_POST['group_id']) || !ctype_digit($_POST['group_id']))
			{
				$this->goError();exit;
			}
			$groupId = $_POST['group_id'];
		}
		else 
		{
			$this->goError();exit;
		}
		
		$roleGroupModel = RolegroupModel::model();
		$group = $roleGroupModel->findByPk($groupId);
		if (empty($group))
		{
			$this->goError();exit;
		}
		return $group;
	}
	
	public function actionRolelist()
	{
		$roleGroup = $this->_getRoleGroup();
		$model = RoleModel::model();
		$roleList = $model->where('role_group_id='.$roleGroup['id'])->order('level ASC')->select();
//		var_dump($roleList);exit;
		$html = $this->render('rolelist', array('roleList' => $roleList, 'roleGroup' => $roleGroup));
		echo $html;
	}
	
	public function actionRoleadd()
	{
		$roleGroup = $this->_getRoleGroup();
		$model = RoleModel::model();
		if (App::ins()->request->isGet())
		{
			$html = $this->render('roleform', array('model' => $model, 'roleGroup' => $roleGroup));
			echo $html;
		}
		elseif (App::ins()->request->isPost())
		{
			$model->scene = 'add';
			self::submit($model, $roleGroup);
		}
	}
	
	public function actionRoleedit()
	{
		$model = RoleModel::model();
		$roleGroup = $this->_getRoleGroup();
		if (App::ins()->request->isGet())
		{
			if (empty($_GET['id']) || !ctype_digit(strval($_GET['id'])))
			{
				$this->goError();
			}
			$action = $this->createUrl('manage/role/roleedit', array('id' => $_GET['id']));
			$role = $model->findByPk($_GET['id']);
			$model->setData($role);//直接赋值，通过getData取出来，重用roleform。
			$html = $this->render('roleform', array('model' => $model, 'roleGroup' => $roleGroup));
			echo $html;
		}
		else
		{
			//提交更改操作
			self::submit($model, $roleGroup);
		}
		
	}
	
	public function submit(&$model, $roleGroup)
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
			if ($_POST['downloaded_limit'] > 0)
			{
				if ($_POST['uploaded_limit']/$_POST['downloaded_limit'] > $_POST['ratio_limit'])
				{
					$model->setError('ratio_limit', '分享率不能小于上传/下载');
					goto A;
				}
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
			$groupId = $_POST['group_id'];
			unset($_POST['group_id']);
			$_POST['role_group_id'] = $groupId;//还是起个相同的名字省事点
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
				$this->redirect('manage/role/rolelist', array('group_id' => $roleGroup['id']));
			}
		}
		A:
		$model->setData($_POST);
		$html = $this->render('roleform', array('model' => $model, 'roleGroup' => $roleGroup));
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
	/**
	 * 为角色添加权限
	 */
	public function actionAddRule()
	{
		if (App::ins()->request->isGet())
		{
			if (empty($_GET['id']) || !ctype_digit($_GET['id']))
			{
				$this->goError();
			}
			$roleInfo = RoleModel::model()->findByPk($_GET['id']);
			if (empty($roleInfo))
			{
				$this->goError();
			}
			
			$ruleModel = RuleModel::model();
			$ruleList = $ruleModel->order('sort ASC,path ASC')->select();
			$ruleHaved = $ruleModel->table('role_rule')->where('role_id='.$_GET['id'])->select();
			if (!empty($ruleHaved))
			{
				$ruleIdHaved = array();
				foreach ($ruleHaved as $ruleHave)
				{
					$ruleIdHaved[] = $ruleHave['rule_id'];
				}
				foreach ($ruleList as &$rule)
				{
					if (in_array($rule['id'], $ruleIdHaved))
					{
						$rule['checked'] = TRUE;
					}
					else
					{
						$rule['checked'] = FALSE;
					}
				}
			}
// 			echo '<pre/>';
// 			var_dump($ruleHaved);
// 			var_dump($ruleList);exit;
			$html = $this->render('addrule', array('ruleList' => $ruleList, 'roleInfo' => $roleInfo));
			echo $html;
		}
		else
		{
			if (!isset($_POST['role_id']) || !ctype_digit($_POST['role_id']))
			{
				echo json_encode(array('code' => -1, 'msg' => 'role_id非法'));exit;
			}
			
			else
			{
				$model = RoleModel::model();
				//先清空之前数据
				$sql = "DELETE FROM role_rule WHERE role_id=".$_POST['role_id'];
				$delete = $model->execute($sql);
				if (empty($_POST['ruleIdList']))
				{
					echo json_encode(array('code' => 0, 'msg' => '没有分配任何权限'));exit;
				}
				$ruleIdArr = explode('_', $_POST['ruleIdList']);
				$sql = "INSERT INTO role_rule (role_id, rule_id) VALUES ";
				foreach ($ruleIdArr as $ruleId)
				{
					$sql .= "({$_POST['role_id']},$ruleId),";
				}
				$sql = rtrim($sql, ",");
				
				$result = $model->execute($sql);
				if ($result === FALSE)
				{
					echo json_encode(array('code' => 0, 'msg' => '插入role_rule失败'));
				}
				elseif($result == 0)
				{
					echo json_encode(array('code' => 0, 'msg' => '没有变化'));
				}
				else
				{
					echo json_encode(array('code' => 1, 'msg' => '保成成功'));
				}
			}
		}
	}

}