<?php
class ForumController extends CommonController
{
	public $layout = 'manage';
	private static $roleList = array();//角色列表
	
	public function actionSectionlist()
	{
		$model = ForumsectionModel::model();
		$sql = "SELECT a.*,b.name as view_limit_name,c.name as reply_limit_name,d.name as publish_limit_name FROM forum_section a LEFT JOIN role b ON a.view_level_limit = b.level 
				LEFT JOIN role c ON a.reply_level_limit=c.level LEFT JOIN role d ON a.publish_level_limit=d.level";
		$parent = NULL;
		if (isset($_GET['parent_id']) && $_GET['parent_id'] != 0)
		{
			if (empty($_GET['parent_id']) || !ctype_digit($_GET['parent_id']))
			{
				$this->goError();
			}
			$parent = $model->findByPk($_GET['parent_id']);
			if (empty($parent))
			{
				$this->goError();
			}
			$sql .= " WHERE a.parent_id=".$_GET['parent_id'];
		}
		else 
		{
			$sql .= " WHERE a.parent_id=0";
		}
		$sql .= " ORDER BY a.sort ASC";
		$sectionList = $model->findBySql($sql);
		$html = $this->render('sectionlist', array('sectionList' => $sectionList, 'parent' => $parent));
		echo $html;
	}
	
	public function actionSectionadd()
	{
		$model = ForumsectionModel::model();
		$action = $this->createUrl('manage/forum/sectionadd');
		if (App::ins()->request->isGet())
		{
			$html = $this->render('sectionform', array('model' => $model, 'action' => $action));
			echo $html;
		}
		elseif(App::ins()->request->isPost())
		{
			self::submit($model, $action);
		}
	}
	
	public function actionSectionedit()
	{
		$model = ForumsectionModel::model();
		if (App::ins()->request->isGet())
		{
			if (empty($_GET['id']) || !ctype_digit(strval($_GET['id'])))
			{
				$this->goError();
			}
			$action = $this->createUrl('manage/forum/sectionedit', array('id' => $_GET['id']));
			$section = $model->findByPk($_GET['id']);
			$model->setData($section);//直接赋值，通过getData取出来，重用roleform。
			$html = $this->render('sectionform', array('model' => $model, 'action' => $action));
			echo $html;
		}
		else
		{
			//提交更改操作
			$action = $this->createUrl('manage/forum/sectionedit', array('id' => $_POST['id']));
			self::submit($model, $action);
		}
	}
	
	function submit($model, $action)
	{
		if ($model->validate($_POST))
		{
			if ($_POST['parent_id'] != 0)
			{
				$parent = $model->findByPk($_POST['parent_id'], 'id, path, level');
				if (empty($parent))
				{
					$model->setError('parent_id', '父版块不存在');
					goto A;
				}
				$_POST['path'] = $parent['path'].','.$parent['id'];
				$_POST['level'] = $parent['level']+1;
			}
			else
			{
				$_POST['path'] = 0;
				$_POST['level'] = 0;
			}
			//检查版主是否正确，并获得id
			$nameArr = preg_split('/\s/', trim($_POST['master_name_list']));
			if (count(array_unique($nameArr)) !== count($nameArr))
			{
				$model->setError('master_name_list', '不要输入重复的值');
				goto A;
			}
			$sql = '';
			foreach ($nameArr as $name)
			{
				$sql .= "SELECT id, name FROM user WHERE name='$name' union ";
			}
			$sql = rtrim($sql, ' union ');
			$masterList = $model->findBySql($sql);
			if (empty($masterList))
			{
				$model->setError('master_name_list', '确保输入的用户名正确');
				goto A;
			}
			$idListStr = '';
			$nameListStr = '';
			$realNameArr = array();
			foreach ($masterList as $master)
			{
				$idListStr .= $master['id'].',';
				$nameListStr .= $master['name'].',';
				$realNameArr[] = $master['name'];
			}
			$diff = array_diff($nameArr, $realNameArr);
			if (!empty($diff))
			{
				$model->setError('master_name_list', implode(',', $diff).'不存在');
				goto A;
			}
		
			$_POST['master_id_list'] = rtrim($idListStr, ',');
			$_POST['master_name_list'] = rtrim($nameListStr, ',');
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
				$model->setError('name', '未知错误，新增失败');
			}
			else
			{
				if (isset($_POST['parent_id']))
				{
					$this->redirect('manage/forum/sectionlist', array('parent_id' => $_POST['parent_id']));
				}
				else 
				{
					$this->redirect('manage/forum/sectionlist');
				}
				
			}
		}
		A:
		$model->setData($_POST);
		$html = $this->render('sectionform', array('model' => $model, 'action' => $action));
		echo $html;
	}
	/**
	 * 获得等级角色下拉列表
	 */
	public function getRoleSelect($name, $selectedValue)
	{
		if(empty(self::$roleList))
		{
			$roleList = self::$roleList = RoleModel::model()->field('id, name, level')->order('level ASC')->select();
		}
		else 
		{
			$roleList = self::$roleList;
		}
		
		$selectHtml = '<select class="form-control" id="view-level_limit" name="'.$name.'">';
		$selectHtml .= '<option value="0">选择一个等级...</option>';
		if (!empty($roleList))
		{
			
			foreach ($roleList as $role)
			{
				$selected = '';
				if ($role['level'] == $selectedValue)
				{
					$selected = " selected";
				}
				$selectHtml .= '<option value="'.$role['level'].'"'.$selected.'>'.$role['name'].'</option>';
			}
		}
		$selectHtml .= '</select>';
		return $selectHtml;
	}
	/**
	 * 获得父版块下拉列表
	 */
	public function getParentSelect($selectedValue, $selfId)
	{
		//只能选一级版块，也就是最多只有二级
		$sectionList = ForumsectionModel::model()->field('id, name')->where('parent_id=0')->order('sort ASC')->select();
		$selectHtml = '<select class="form-control" id="parent_id" name="parent_id">';
		$selectHtml .= '<option value="0">无(新增为一级版块)</option>';
		if (!empty($sectionList))
		{
			foreach ($sectionList as $section)
			{
				$selected = '';
				if ($section['id'] == $selectedValue)
				{
					$selected = "selected";
				}
				if (empty($selfId) || (!empty($selfId) && $section['id'] != $selfId))//去除自己
				{
					$selectHtml .= '<option value="'.$section['id'].'"'.$selected.'>'.$section['name'].'</option>';
				}
			}
		}
		$selectHtml .= '</select>';
		return $selectHtml;
	}
}