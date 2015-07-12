<?php
namespace application\protect\controllers;

use framework\App;
use application\protect\models as models;

class CommentController extends CommonController
{
	public function actionAdd()
	{
		if (App::ins()->request->isPost())
		{
			if (empty($_POST['torrentId']) || !ctype_digit($_POST['torrentId']) || empty($_POST['content']))
			{
				$this->goError('参数错误');
			}
			//先添加楼层
			$userId = App::ins()->user->getId();
			$userName = App::ins()->user->getName();
			$content = nl2br(htmlspecialchars($_POST['content']));
			$floorData = array(
					'torrent_id' => $_POST['torrentId'],
					'user_id' => $userId,
					'user_name' => $userName,
					'content' => $content,
					'add_time' => TIME_NOW,
					'path' => '/',
					'position' => 1,
			);
			$floorModel = models\CommenFloortModel::model();
			$buildingFloors = '';//楼栋的floors
			if (!empty($_POST['parentId']))
			{
				if (!ctype_digit($_POST['parentId']))
				{
					$this->goError('parentId错误');
				}
				$parentFloor = $floorModel->findByPk($_POST['parentId']);
				if (empty($parentFloor))
				{
					$this->goError('父楼层不存在');
				}
				$floorData['path'] = $parentFloor['path'].$parentFloor['id'].'/';
				$floorData['position'] = $parentFloor['position'] + 1;
				$buildingFloors = $parentFloor['floors'];
			}
			$floorModel->beginTransaction();//开启事务
			$addFloor = $floorModel->insert($floorData);
			if (!$addFloor)
			{
				$floorModel->rollBack();
				$this->goError('添加楼层失败');
			}
			//再添加楼栋
			$buildingModel = models\CommentBuildingModel::model();
			$buildingPosition = $buildingModel->getMaxPosition();
			$buildingData = array(
					'position' => $buildingPosition + 1,
					'add_time' => TIME_NOW,
					'floors' => empty($buildingFloors) ? $addFloor : $buildingFloors.','.$addFloor,
					'torrent_id' => $_POST['torrentId'],
					'user_id' => $userId,
					'user_name' => $userName,
			);
			$addBuilding = $buildingModel->insert($buildingData);
			if (!$addBuilding)
			{
				$floorModel->rollBack();
				$this->goError('添加楼栋失败');
			}
			$floorModel->commit();
			//取出这一栋的信息用于返回
			$floorList = $floorModel->where('id IN ('.$buildingData['floors'].')')->order('position ASC')->select();
			$floorListIdKey = array();
			foreach ($floorList as &$floor)
			{
				$floorListIdKey[$floor['id']] = $floor;
			}
			unset($floor);
			unset($floorList);
			$buildingData['floors'] = explode(',', $buildingData);//转化为数组
			$html = $this->renderPartial('comment_onebuilding', array('building' => $buildingData, 'floorListIdKey' => $floorListIdKey));
			echo json_encode(array('code' => 1, 'msg' => '添加楼层成功', 'data' => $html));		
		}
		else
		{
			$this->goError('非法请求');
		}
	}
	
	public function actionList()
	{
		if (App::ins()->request->isGet())
		{
			if (empty($_GET['torrentId']))
			{
				echo json_encode(array('code' => -1, 'msg' => '参数不全'));exit;
			}
			$page = empty($_GET['page']) ? 1 : $_GET['page'];
			$per = 5;//每页显示8条评论
			$offset = ($page-1)*$per;
			$model = models\CommentModel::model();
			$navHtml = 0;
			$total = 0;
			$count = 0;
			
			$count = $model->where('torrent_id=:torrentId', array(':torrentId' => $_GET['torrentId']))->count();
			if ($count == 0)//没有时是string类型的0，使用===时候要注意！
			{
				echo json_encode(array('code' => 0, 'msg' => '暂无评论'));exit;
			}
			$total = ceil($count/$per);
			$navHtml = $this->getAjaxNavHtml($page, $total);
			
			
			$sql = "SELECT a.*, b.name as user_name, b.avatar_url as user_avatar, b.id as user_id FROM comment as a LEFT JOIN user as b ON a.user_id = b.id WHERE a.torrent_id = :torrentId ORDER BY a.floor ASC LIMIT $offset, $per";
			$comments = $model->findBySql($sql, array(':torrentId' => $_GET['torrentId']));
			
			$html = $this->renderPartial('comment', array('comments' => $comments, 'navHtml' => $navHtml, 'floor' => $count, 'page' => $total));
			echo json_encode(array('code' => 1, 'msg' => $html));
		}
	}
}