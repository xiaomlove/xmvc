<?php
namespace application\protect\models;

use framework\App;

class CategoryModel extends \framework\core\Model
{

	public function tableName()
	{
		return 'category';	
	}
	
	public static function model()
	{
		return parent::getModel(__CLASS__);
	}
	
	public function rules()
	{
		return array(
			array('name, value', 'required', '不能为空'),
			array('value', 'number', '必须是数字值'),
		);
	}
	/**
	 * 查找某一父分类下的所有子项目
	 * @param string $parentField
	 * @return array
	 */
	public function getByParentField($parentField)
	{
		$table = self::tableName();
		$sql = "SELECT * FROM $table WHERE parent_id=(SELECT id FROM $table WHERE value='$parentField' AND parent_id=0 LIMIT 1) ORDER by sn ASC,id ASC";
		return $this->findBySql($sql);
	}
	/**
	 * 获得父分类与子分类
	 * @return array 返回父分类，二维数组，父分类的subs属性存储其子分类
	 */
	public function getParentSubTree()
	{
		$parentList = $this->where('parent_id=0')->order('sn ASC')->select();
		if (empty($parentList))
		{
			return array();
		}
		$subList = $this->where('parent_id>0')->order('sn ASC')->select();
		foreach ($parentList as &$parent)
		{
			$parent['subs'] = array_filter($subList, function($sub) use(&$parent) {
				return $sub['parent_id'] == $parent['id'];
			});
		}
		return $parentList;
	}
	
	public function createSearchBox()
	{
		$categoryData = self::getParentSubTree();
		if (empty($categoryData))
		{
			return '';
		}
		$boxHtml = '<table class="table table-bordered search-box">';
		$boxHtml .= '<thead><tr><th colspan="2" class="search-box-title"><span class="search-box-icon" title="点击收缩或展开"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span>搜索箱</span></th></tr></thead>';
		$boxHtml .= '<tbody class="category-box"><tr><td>';
		$boxHtml .= '<div>';
		foreach ($categoryData as $category)
		{
			$boxHtml .= '<div class="category-item"><div class="parent-category">';
			$boxHtml .= '<strong>'.$category['name'].'</strong><button type="button" class="btn btn-default btn-xs select-all">全选</button>';
			$boxHtml .= '</div><div class="sub-category">';
			if (!empty($category['subs']))
			{
				$boxHtml .= '<ul class="list-unstyled list-inline">';
				foreach ($category['subs'] as $sub)
				{
					if ($category['value'] === 'source_type')
					{
						$boxHtml .= '<li title="'.$sub['name'].'"><input type="checkbox" name="'.$category['value'].'" value="'.$sub['value'].'"><span class="category-icon" style="background-image: url(\''.(empty($sub['icon_src']) ? '/application/assets/images/catsprites.png' : $sub['icon_src']).'\')"></span></li>';
					}
					else
					{
						$boxHtml .= '<li><input type="checkbox" name="'.$category['value'].'" value="'.$sub['value'].'">'.$sub['name'].'</li>';
					}
				}
				$boxHtml .= '</ul>';
			}
			$boxHtml .= '</div></div>';//完成一个category-item
		}
		$boxHtml .= '</div>';//完成categorybox
		$boxHtml .= '</td>';//完成左边td
		$boxHtml .= '<td>';//开始右边td
		$boxHtml .= '<div class="right-item"><strong>活动状态</strong><select name="active-state"><option value="1">全部</option><option value="2">仅活种</option><option value="3">仅死种</option></select></div>';
		$boxHtml .= '<div class="right-item"><strong>促销状态</strong><select name="sp-state"><option value="1">全部</option><option value="2">正常</option><option value="3">50%</option></select></div>';
		$boxHtml .= '</td>';//完成右边td		
		$boxHtml .= '</tr></tbody>';//完成分类tbody
		
		$boxHtml .= '<tbody class="input-area"><tr><td>';
		
		//关键字输入区
		$boxHtml .= '<div>搜索关键字：<input type="text" name="keyword">';
		$boxHtml .= '<span>范围：<select name="range"><option value="1">标题</option><option value="2">描述</option><option value="3">发布者</option><option value="4">IMDB</option></select></span>';	
		$boxHtml .= '</div>';//输入区结束
		
		//热门关键字
		$boxHtml .= '<div class="hot-words"><small><a href="#">冲锋车</a></small><small><a href="#">盗墓笔记</a></small><small><a href="#">超能查派</a></small><small><a href="#">侏罗纪世界</a></small><small><a href="#">奔跑吧兄弟</a></small></div>';
		
		
		$boxHtml .= '</td><td>';
		$boxHtml .= '<button type="button" class="btn btn-success">给我搜</button>';
		$boxHtml .= '</td></tr></tbody>';
		$boxHtml .= '</table>';
		return $boxHtml;
	}
	

	/**
	 * 添加一级分类
	 * Enter description here ...
	 * @param string $name 一级分类名称
	 * @param string $field 对应torrent字段，需要先在数据库添加
	 */
	public function addParent($name, $field)
	{
		$maxSn = self::getMaxSn() + 1;
		$name = strip_tags($name);
		$sql = "INSERT INTO ".self::tableName()." (name,sn,value) VALUES ('$name','$maxSn','$field')";
		return $this->execute($sql);
	}
	/**
	 * 获得当前最大的排序序号，首条记录默认99
	 * Enter description here ...
	 */
	public function getMaxSn($parentId = '')
	{
		$sql = 'SELECT max(sn) as maxSn FROM '.self::tableName();
		if (!empty($parentId))
		{
			$sql .= ' WHERE parent_id='.$parentId;
		}
		$result = $this->findBySql($sql);
		return empty($result[0]['maxSn']) ? 99 : $result[0]['maxSn'];
	}
	/**
	 * 交换两个分类的排序序号sn
	 * @param unknown $id  自身id
	 * @param unknown $targetId  目标id
	 * @return boolean  成功TRUE失败FALSE
	 */
	public function exchangeSn($id, $targetId)
	{
		$self = $this->active()->findByPk($id);
		if (empty($self))
		{
			return FALSE;
		}
		$target = $this->active()->findByPk($targetId);
		if (empty($target))
		{
			return FALSE;
		}
		$selfSn = $self->sn;
		$targetSn = $target->sn;
		$this->beginTransaction();
		$self->sn = $targetSn;
		$target->sn = $selfSn;
		$updateSelf = $self->save();
		$updateTarget = $target->save();
		if ($updateSelf !== FALSE && $updateTarget !== FALSE)
		{
			$this->commit();
			return TRUE;
		}
		else
		{
			$this->rollBack();
			return FALSE;
		}
	}
	
	public function hashPassword($password)
	{
		if(empty($password) || !is_string($password))
		{
			return FALSE;
		}
		if(function_exists('password_hash'))
		{
			return password_hash($password, PASSWORD_DEFAULT);
		}
		else
		{
			//App::addRequirePath(LIB_PATH.'phpass-0.3'.DS);
			$hasher = new \framework\lib\phpass\PasswordHash(8, false);
			$hashPassword = $hasher->HashPassword($password);
			return $hashPassword;
		}
		
	}
	
	public function checkPassword($inputPassword, $password)
	{
		if(function_exists('password_verify'))
		{
			return password_verify($inputPassword, $password);
		}
		else
		{
			$hasher = new \framework\lib\phpass\PasswordHash(8, false);
			return $hasher->CheckPassword($inputPassword, $password);
			
		}
	}
	
	/**
	 * 获得用户拥有的角色
	 * @param return array 二维数组，每个角色为一个元素
	 */
	public function getRoles($userId = '')
	{
		$isLogin = App::ins()->user->isLogin();
		if ($isLogin)
		{
			if (empty($userId))
			{
				$userId = App::ins()->user->getId();
			}
			$sql = "SELECT a.*,b.name as role_group_name FROM role a LEFT JOIN role_group b 
					ON a.role_group_id=b.id WHERE a.id IN 
					(SELECT role_id FROM user_role WHERE user_id=$userId)";
			return $this->findBySql($sql);
		}
		else
		{
			$result = $this->table('role')->where('role_group_id='.RolegroupModel::ROLE_GROUP_NORMAR)->order('level ASC')->limit(1)->select();
			return empty($result) ? NULL : $result;
		}
		
	}
	/**
	 * 获得额外的权限，除角色外的权限
	 * Enter description here ...
	 * @param unknown_type $userId
	 * @param return array
	 */
	public function getExtraRules($userId = '')
	{
		$isLogin = App::ins()->user->isLogin();
		if ($isLogin)
		{
			if (empty($userId))
			{
				$userId = App::ins()->user->getId();
			}
			$sql = "SELECT * FROM rule WHERE id IN (SELECT rule_id FROM user_rule WHERE user_id=$userId)";
			$result = $this->findBySql($sql);
			return $result;
		}
		else
		{
			return array();
		}
	}
	
	/**
	 * 获得当前用户的所有权限，角色上的+额外的。不登陆为游客的
	 */
	public function getRules()
	{
		$roles = self::getRoles();//角色
 	
		if (empty($roles))
		{
			return NULL;
		}

		$roleId = array();
		foreach ($roles as $role)
		{
			$roleId[] = $role['id'];
		}
		$rules = RuleModel::model()->getRulesByRole($roleId);
//		var_dump($rules);
//		echo '<hr/>';
		$extraRules = self::getExtraRules();
//		echo '<hr/>';
//		var_dump($extraRules);
		$merge = array_merge($rules, $extraRules);
//		echo '<hr/>';
//		var_dump($merge);
//		exit;
		return $merge;
	}
	
}