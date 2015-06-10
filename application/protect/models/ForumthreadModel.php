<?php
namespace application\protect\models;

class ForumthreadModel extends \framework\core\Model
{
	const STATE_DELETE = -1;//删除
	const STATE_DRAFT = 0;//草稿
	const STATE_PUBLISH = 1;//发表
	const STATE_LOCK = 2;//锁定
	
	const IS_TOP = 1;//置顶
	const IS_NOT_TOP = 0;//非置顶
	
	public function tableName()
	{
		return 'forum_thread';
	}
	
	//子类获得模型对象的方法，通过调用父类的getModel()，传递子类的类名
	public static function model($className = __CLASS__)
	{
		return parent::getModel($className);
	}
	
	//array(字段名， 验证规则， 错误信息，[[附加条件], [验证场景]])
	//前三个必填，如果规则中是in，range等需要相应的值的，则写到附加条件，否则附加条件不需要填写。
	//验证场景如果有添加，则该规则只有开始验证前由模型指定为场场景才应用此验证，否则不应用。为一个键值对，如'on'=>'edit'
	public function rules()
	{
		return array(
			array('title, content', 'required', '不能为空！'),
		);
	}
	
	/**
	 * 获得某主题的回复列表
	 * @param array $condition
	 * @return multitype:unknown
	 */
	public function getReplyList(array $condition)
	{
		if (!isset($condition['per_page']))
		{
			$per = OptionModel::model()->get('forum_reply_pagination');
		}
		else 
		{
			$per = $condition['per_page'];
			unset($condition['per_page']);
		}
		$default = array(
				'page'=>1,
				'sort_field'=>'floor',
				'sort_type'=>'asc'
		);
		foreach ($default as $key => $value)
		{
			if (isset($condition[$key]))
			{
				$default[$key] = $condition[$key];
			}
		}
		$offset = ((int)$default['page'] - 1) * $per;
		$sortField = $default['sort_field'];
		$sortType = strtoupper($default['sort_type']);
		$sql = "select aa.*,bb.name,bb.role_name,bb.uploaded,bb.downloaded,bb.thread_count,bb.reply_count as user_info_reply_count,bb.comment_count,bb.avatar_url FROM forum_reply aa LEFT JOIN user bb ON aa.user_id=bb.id WHERE aa.thread_id=".$_GET['thread_id'];
		$sql .= " ORDER BY $sortField $sortType";
		
		$sql .= " LIMIT $offset, $per";
	
		$result = $this->findBySql($sql);
		$sql = "SELECT count(*) as count FROM forum_reply WHERE section_id={$_GET['section_id']} AND thread_id={$_GET['thread_id']}";
		$count = $this->findBySql($sql);
// 		var_dump($count);exit;
		return array('data' => $result, 'count' => $count[0]['count'], 'per_page' => $per);
	
	}
	
	
	/**
	 * 获得主题列表
	 * @param array $condition
	 * @return multitype:unknown
	 */
	public function getThreadList(array $condition)
	{
		if (!isset($condition['per_page']))
		{
			$per = OptionModel::model()->get('forum_thread_pagination');
		}
		else
		{
			$per = $condition['per_page'];
			unset($condition['per_page']);
		}
		$default = array(
				'page'=>1,
				'filter' => 0,
				'sort_field'=>'last_reply_time',
				'sort_type'=>'desc'
		);
		foreach ($default as $key => $value)
		{
			if (isset($condition[$key]))
			{
				$default[$key] = $condition[$key];
			}
		}
		$offset = ((int)$default['page'] - 1) * $per;
		$sortField = $default['sort_field'];
		$sortType = strtoupper($default['sort_type']);
		if ($default['page'] == 1)
		{
			//第一页优先取置顶
			$sql = "SELECT t.*,user.name as user_name FROM (SELECT top.* FROM (SELECT * FROM forum_thread WHERE section_id={$_GET['section_id']} AND is_top=".self::IS_TOP." AND state=".self::STATE_PUBLISH." ORDER BY top_sort ASC) top 
					 UNION SELECT main.* FROM (SELECT * FROM forum_thread WHERE section_id={$_GET['section_id']} AND is_top=".self::IS_NOT_TOP." AND state=".self::STATE_PUBLISH;
			if (!empty($default['filter']) && ($default['filter'] === 'add_time' || $default['filter'] === 'support_count'))
			{
				$sql .= " ORDER BY {$default['filter']} DESC,$sortField $sortType";
			}
			else
			{
				$sql .= " ORDER BY $sortField $sortType";
			}
			$sql .= ") main) t LEFT JOIN user ON t.user_id=user.id";
		}
		else 
		{
			$sql = "SELECT a.*,b.name as user_name FROM forum_thread a LEFT JOIN user b ON a.user_id=b.id WHERE a.section_id={$_GET['section_id']} AND a.state=".self::STATE_PUBLISH;
			if (!empty($default['filter']) && ($default['filter'] === 'add_time' || $default['filter'] === 'support_count'))
			{
				$sql .= " ORDER BY {$default['filter']} DESC,$sortField $sortType";
			}
			else
			{
				$sql .= " ORDER BY $sortField $sortType";
			}
		}
		
		$sql .= " LIMIT $offset, $per";
		$result = $this->findBySql($sql);
		$sql = "SELECT count(*) as count FROM forum_thread WHERE section_id={$_GET['section_id']} AND state=".self::STATE_PUBLISH;
		$count = $this->findBySql($sql);
		// 		var_dump($count);exit;
		return array('data' => $result, 'count' => $count[0]['count'], 'per_page' => $per);
	
	}
	
	
	
}