<?php
namespace application\protect\models;

use framework\App;

class TorrentModel extends \framework\core\Model
{
	private static $_searchFields;
	
	public function tableName()
	{
		return 'torrent';	
	}
	
	public static function model()
	{
		return parent::getModel(__CLASS__);
	}
	
	public function init()
	{
		self::$_searchFields = array(
			'1' => array('main_title', 'slave_title'),
			'2' => 'introduce',
			'3' => 'b.name',
			'4' => 'imdb_id',
		);
	}
	
	public function rules()
	{
		return array(
			array('slave_title, introduce', 'required', '不能为空！'),
			array('slave_title', 'length', '长度请控制在10~100字符！', array('max'=>100, 'min'=>10)),
			array('source_type, source_medium, imdb_rate, video_encode, audio_encode, resolution, team, year, region', 'range', '请选择正确一项', array('min' => 1)),
		);
	}
	/**
	 * 一个公用的返回各种搜索条件结果的方法
	 * Enter description here ...
	 * @param array $condition 分页条件，包括page,per,sortField,sortType
	 * @param string $where  where中的限制条件
	 * @return array 返回总记录条数以及当前页的数据及分页条件
	 */
	public function getList(array $condition = array(), $where = '')
	{
		$per = OptionModel::model()->get('torrent_list_pagination');
		$default = array(
			'page'=>1,
			'per'=> $per,
			'sort_field'=>'add_time',
			'sort_type'=>'desc'
		);
		foreach ($default as $key => $value)
		{
			if (isset($condition[$key]))
			{
				$default[$key] = $condition[$key];
			}
		}
		$per = $default['per'];
		$offset = ((int)$default['page'] - 1) * $per;
		$sortField = $default['sort_field'];
		if(stripos($sortField, 'user_name') !== FALSE)
		{
			$sortField = 'b.name';
		}
		else 
		{
			$sortField = 'a.'.$sortField;
		}
		$sortType = strtoupper($default['sort_type']);	
		$sql = "SELECT a.id, a.main_title, a.slave_title, a.add_time, a.size, a.seeder_count, a.leecher_count, a.finish_times, a.comment_count, a.view_times, a.user_id, b.name as user_name FROM torrent as a LEFT JOIN user as b ON a.user_id = b.id ";
		$countSql = "SELECT count(*) as count FROM torrent ";
		$filterFields = CategoryModel::model()->getFilterFields();
		$filter = '';
		//分类
		foreach ($condition as $k => $v)
		{
			if (in_array($k, $filterFields) && !empty($v) && preg_match('/^\d(\d|,)*$/', $v))
			{
				if ($v == ',')
				{
					continue;
				}
				if (strpos($v, ',') !== FALSE)
				{
					$filter .= "$k IN($v) AND ";				
				}
				else 
				{
					$filter .= "$k=$v AND ";
				}
			}
		}
		//关键字
		if (!empty($condition['keyword']) && !empty($condition['range']) && ctype_digit(strval($condition['range'])))
		{
			$keywordArr = preg_split('/\s+/', $condition['keyword']);
			$range = $condition['range'];
			
			if (isset(self::$_searchFields[$range]))
			{
				if ($range == '3')//搜索发布者，需要连表
				{
					$countSql .= "as a  LEFT JOIN user as b ON a.user_id = b.id ";
				}
				$field = self::$_searchFields[$range];
				foreach ($keywordArr as $keyword)
				{
					if (is_array($field))
					{
						$filter .= "(";
						foreach ($field as $fieldItem)
						{
							$filter .= "$fieldItem LIKE '%$keyword%' OR ";
						}
						$filter = rtrim($filter, 'OR ');
						$filter .= ") AND ";
					}
					elseif (is_string($field))
					{
						$filter .= "$field LIKE '%$keyword%' AND ";
					}
				}
			}
		}
		//右边状态
		if (!empty($condition['active_state']) && ctype_digit(strval($condition['active_state'])))
		{
			if ($condition['active_state'] == 1)
			{
				$filter .= "seeder_count > 0 AND ";
			}
			elseif ($condition['active_state'] == 2)
			{
				$filter .= "seeder_count = 0 AND ";
			}
			
		}
		//促销这些暂时略
		
		$filter = rtrim($filter, 'AND ');
		
		if (!empty($where))
		{
			if (!empty($filter))
			{
				$sql .= "WHERE $where AND $filter ";
				$countSql .= "WHERE $where AND $filter ";
// 				$count = $this->where("$where AND $filter")->count();
			}
			else
			{
				$sql .= "WHERE $where ";
				$countSql .= "WHERE $where ";
// 				$count = $this->where("$where")->count();
			}
		}
		else 
		{
			if (!empty($filter))
			{
				$sql .= "WHERE $filter ";
				$countSql .= "WHERE $filter ";
// 				var_dump($filter);exit;
// 				$count = $this->where("$filter")->count();
			}
			else
			{
				$sql .= "WHERE 1 ";
// 				$count = $this->count();
			}
		}
		
		$sql .= "ORDER BY $sortField $sortType ";
		$sql .= "LIMIT $offset, $per";
		
		$result = $this->findBySql($sql);
		$count = $this->findBySql($countSql);
		return array('data' => $result, 'count' => $count[0]['count'], 'page' => $default['page'], 'per' => $per);
		
	}
	
	public function getTorrent($id)
	{
		$info = $this->findByPk($id, 'name');
		$path = substr($info['name'], 0, 8);//前8位是文件夹名，时间不算了
		$path = App::getPathOfAlias(App::getConfig('torrentSavePath')).$path.DS;//种子保存路径
		$file = $path.$info['name'];//种子文件
		$encodeFile = \framework\helper\StringHelper::encodeFileName($file);
//		var_dump($file);
		if (file_exists($encodeFile))
		{
			return $file;//只是file_exists()判断时需要转换一下判断，返回还是得原始的
		}
		else
		{
			return NULL;
		}
	}
	
	
	
	
}