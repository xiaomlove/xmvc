<?php
namespace application\protect\models;

//楼栋模型

class CommentBuildingModel extends \framework\core\Model
{
	public function tableName()
	{
		return 'comment_building';	
	}
	
	public static function model()
	{
		return parent::getModel(__CLASS__);
	}
	
	
	public function getMaxPosition()
	{
		$sql = "SELECT max(position) as maxPosition FROM ".self::tableName();
		$result = $this->findBySql($sql);
		return empty($result) ? 0 : $result[0]['maxPosition'];
	}
	
	public function getList($start, $count, $order, $condition = '')
	{
		$sql = "SELECT * FROM comment_building WHERE $condition ORDER BY $order LIMIT $start,$count";
		$buildingList = $this->findBySql($sql);
		if (empty($buildingList))
		{
			return array();
		}
		$total = $this->where($condition)->count();
		$floors = '';
		foreach ($buildingList as &$building)
		{
			$floors .= $building['floors'].',';
			$building['floors'] = explode(',', $building['floors']);
		}
		unset($building);
		reset($buildingList);
		
		$floors = rtrim($floors, ',');
		$floors = implode(',', array_unique(explode(',', $floors)));
		$sql = "SELECT * FROM comment_floor WHERE id IN ($floors) ORDER BY position ASC";
		$floorList = $this->findBySql($sql);
		$floorListIdKey = array();
		foreach ($floorList as &$floor)
		{
			$floorListIdKey[$floor['id']] = $floor;
		}
		unset($floor);
		reset($floorList);
		return array('buildingList' => $buildingList, 'total' => $total, 'floorList' => $floorListIdKey);
	}
	
}