<?php
class TorrentModel extends Model
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
	 */
	public function getList(array $condition)
	{
		$default = array(
			'page'=>1,
			'per_page'=>10,
			'sort_field'=>'add_time',
			'sort_type'=>'asc'
		);
		$diff = array_diff_assoc($default, $condition);//差集
		$condition = array_merge($default, $condition);//合并
		$condition = array_diff_assoc($condition, $diff);//去掉多余的
		
		
		
		
	}
	
	
	
	
	
}