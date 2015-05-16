<?php
namespace application\protect\models;

use framework;
use framework\App as App;

class TorrentModel extends framework\core\Model
{
	public function tableName()
	{
		return 'torrent';	
	}
	
	public static function model()
	{
		return parent::getModel(__CLASS__);
	}
	
	public function rules()
	{
		return array(
			array('main_title, slave_title, introduce', 'required', '不能为空！'),
			array('main_title, slave_title', 'length', '长度请控制在10~100字符！', array('max'=>100, 'min'=>10)),
		);
	}
	/**
	 * 一个公用的返回各种搜索条件结果的方法
	 * Enter description here ...
	 * @param array $condition
	 * @return array 返回总记录条数以及当前页的数据
	 */
	public function getList(array $condition)
	{
		$default = array(
			'page'=>1,
			'per_page'=>5,
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
		$per = $default['per_page'];
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
		$sql .= "ORDER BY $sortField $sortType ";
		$sql .= "LIMIT $offset, $per";
		
		$result = $this->findBySql($sql);
		$count = $this->count();
		return array('data' => $result, 'count' => $count);
		
	}
	
	public function getTorrent($id)
	{
		$info = $this->findByPk($id, 'name');
		$path = substr($info['name'], 0, 8);//前8位是文件夹名，时间不算了
		$path = App::getPathOfAlias(App::getConfig('torrentSavePath')).$path.DS;//种子保存路径
		$file = $path.$info['name'];//种子文件
		$encodeFile = framework\helper\StringHelper::encodeFileName($file);
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