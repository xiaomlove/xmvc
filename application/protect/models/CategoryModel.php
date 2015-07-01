<?php
namespace application\protect\models;

use framework\core\Router;
class CategoryModel extends \framework\core\Model
{
	private static $_categoryInfo = array();//分类父子Tree信息

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
		if (!empty(self::$_categoryInfo))
		{
			return self::$_categoryInfo;
		}
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
		self::$_categoryInfo = $parentList;
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
		$typeUrl = Router::createUrl('torrent/list');
		foreach ($categoryData as $category)
		{
			$valueArr = array();
			if (!empty($_GET[$category['value']]) && preg_match('/^\d(\d|,)*$/', $_GET[$category['value']]))
			{
				$valueArr = explode(',', $_GET[$category['value']]);
			}
			$boxHtml .= '<div class="category-item" data-field="'.$category['value'].'"><div class="parent-category">';
			$boxHtml .= '<strong>'.$category['name'].'</strong><button type="button" class="btn btn-default btn-xs select-all">全选</button>';
			$boxHtml .= '</div><div class="sub-category">';
			if (!empty($category['subs']))
			{
				$boxHtml .= '<ul class="list-unstyled list-inline">';
				foreach ($category['subs'] as $sub)
				{
					$checked = '';
					if (in_array($sub['value'], $valueArr))
					{
						$checked = ' checked';
					}
					if ($category['value'] === 'resource_type')
					{
						$boxHtml .= '<li title="'.$sub['name'].'"><input type="checkbox" name="'.$category['value'].'" value="'.$sub['value'].'"'.$checked.'><a href="'.$typeUrl.'?'.$category['value'].'='.$sub['value'].'"><span class="category-icon" style="background-image: url(\''.(empty($sub['icon_src']) ? '/application/assets/images/catsprites.png' : $sub['icon_src']).'\')"></span></a></li>';
					}
					else
					{
						$boxHtml .= '<li><label><input type="checkbox" name="'.$category['value'].'" value="'.$sub['value'].'"'.$checked.'>'.$sub['name'].'</label></li>';
					}
				}
				$boxHtml .= '</ul>';
			}
			$boxHtml .= '</div></div>';//完成一个category-item
		}
		$boxHtml .= '</div>';//完成categorybox
		$boxHtml .= '</td>';//完成左边td
		$boxHtml .= '<td>';//开始右边td
		$activeState = -1;
		if (!empty($_GET['active_state']) && ctype_digit($_GET['active_state']))
		{
			$activeState = $_GET['active_state'];
		}
		$boxHtml .= '<div class="right-item"><strong>活动状态</strong><select name="active-state"><option value="0">全部</option><option value="1"'.($activeState === '1' ? ' selected' : '').'>仅活种</option><option value="2"'.($activeState === '2' ? ' selected' : '').'>仅死种</option></select></div>';
		$boxHtml .= '<div class="right-item"><strong>促销状态</strong><select name="sp-state"><option value="0">全部</option><option value="1">正常</option><option value="2">50%</option></select></div>';
		$boxHtml .= '</td>';//完成右边td		
		$boxHtml .= '</tr></tbody>';//完成分类tbody
		
		$boxHtml .= '<tbody class="input-area"><tr><td>';
		
		//关键字输入区
		$boxHtml .= '<div>搜索关键字：<input type="text" name="keyword" value="'.(!empty($_GET['keyword']) ? $_GET['keyword'] : '').'">';
		$range = -1;
		if (!empty($_GET['range']) && ctype_digit($_GET['range']))
		{
			$range = $_GET['range'];
		}
		$boxHtml .= '<span>范围：<select name="range"><option value="1"'.($range === '1' ? ' selected' : '').'>标题</option><option value="2"'.($range === '2' ? ' selected' : '').'>描述</option><option value="3"'.($range === '3' ? ' selected' : '').'>发布者</option><option value="4"'.($range === '4' ? ' selected' : '').'>IMDB</option></select></span>';	
		$boxHtml .= '</div>';//输入区结束
		
		//热门关键字
		$boxHtml .= '<div class="hot-words"><small><a href="#">冲锋车</a></small><small><a href="#">盗墓笔记</a></small><small><a href="#">超能查派</a></small><small><a href="#">侏罗纪世界</a></small><small><a href="#">奔跑吧兄弟</a></small></div>';
		
		
		$boxHtml .= '</td><td>';
		$boxHtml .= '<button type="button" id="search-btn" class="btn btn-success">给我搜</button>';
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
	/**
	 * 返回用于筛选的父分类
	 * @return array
	 */
	public function getFilterFields()
	{
		$parentCategory = $this->field('value')->where('parent_id=0')->select();
		$out = array();
		if (!empty($parentCategory))
		{
			if (function_exists('array_column'))
			{
				$out = array_column($parentCategory, 'value');
			}
			else
			{
				foreach ($parentCategory as &$category)
				{
					$out[] = $category['value'];
				}
			}
			
		}
		return $out;
	}
	
}