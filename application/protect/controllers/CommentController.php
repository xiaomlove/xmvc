<?php
namespace application\protect\controllers;

use framework\App;
use application\protect\models as models;

class CommentController extends CommonController
{
	public $layout = 'tinypt';
	
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
					'content' => $_POST['content'],
					'add_time' => TIME_NOW,
					'path' => '/',
					'position' => 1,
			);
			$floorModel = models\CommentFloorModel::model();
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
				$buildingFloors = trim(str_replace('/', ',', $floorData['path']), ',');
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
			$buildingData['floors'] = explode(',', $buildingData['floors']);//转化为数组
			$buildingData['id'] = $addBuilding;
			$html = $this->renderPartial('comment_onebuilding', array('building' => $buildingData, 'floorList' => $floorListIdKey));
			echo json_encode(array('code' => 1, 'msg' => '添加楼层成功', 'data' => $html));		
		}
		else
		{
			$this->goError('非法请求');
		}
	}
	
	public function actionHot()
	{
		if (empty($_GET['torrentId']) || !ctype_digit($_GET['torrentId']))
		{
			$this->goError('参数错误');
		}
		$model = models\CommentFloorModel::model();
		$result = $model->where(array('torrent_id' => $_GET['torrentId']))->order('up_count DESC,id DESC')->limit(5)->select();
		if (empty($result))
		{
			echo json_encode(array('code' => 0, 'msg' => '暂无评论'));exit;
		}
		$html = $this->renderPartial('comment_hot', array('buildingList' => $result));
		echo json_encode(array('code' => 1, 'msg' => '获取热门评论成功', 'data' => $html));
	}
	
	public function actionVote()
	{
		if (App::ins()->request->isPost())
		{
			$valueArr = array(1, -1);
			if (empty($_POST['value']) || !in_array($_POST['value'], $valueArr) || empty($_POST['floorId']) || !ctype_digit($_POST['floorId']))
			{
				$this->goError('参数错误');
			}
			$model = models\CommentFloorModel::model();
			if ($_POST['value'] == 1)
			{
				$vote = $model->where(array('id' => $_POST['floorId']))->setInc('up_count');
			}
			elseif ($_POST['value'] == -1)
			{
				$vote = $model->where(array('id' => $_POST['floorId']))->setInc('down_count');
			}
			else
			{
				$this->goError('value错误');
			}
			if ($vote)
			{
				echo json_encode(array('code' => 1, 'msg' => '表态成功'));
			}
			else
			{
				echo json_encode(array('code' => -1, 'msg' => '表态失败'));
			}
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
			if (empty($_GET['torrentId']) || !ctype_digit($_GET['torrentId']))
			{
				echo json_encode(array('code' => -1, 'msg' => '参数不全'));exit;
			}
			$torrentInfo = models\TorrentModel::model()->field('id,name')->findByPk($_GET['torrentId']);
			if (empty($torrentInfo))
			{
				$this->goError('种子不存在');
			}
			$page = empty($_GET['page']) ? 1 : $_GET['page'];
			$per = models\OptionModel::model()->get('comment_list_pagination');
			$offset = ($page-1)*$per;
			$model = models\CommentBuildingModel::model();
			$result = $model->getList($offset, $per, 'position ASC', 'torrent_id='.$_GET['torrentId']);
			if (empty($result))
			{
				echo json_encode(array('code' => 0, 'msg' => '暂无评论'));exit;
			}
			$pageCount = ceil($result['total']/$per);
			$navHtml = $this->getNavHtml($page, $per, $pageCount);
			
			$html = $this->render('comment', array('buildingList' => $result['buildingList'], 'floorList' => $result['floorList'], 'pagination' => $navHtml, 'torrentInfo' => $torrentInfo));
			echo $html;
		}
		else 
		{
			$this->goError('非法请求');
		}
	}
	
	public function renderQuote($floorIdArr, array $floorInfoArr)
	{
		$HTML = '<blockquote class="comment-quote">';
		$floorId = array_pop($floorIdArr);
		if (!empty($floorIdArr))
		{
			$HTML .= $this->_renderQuote($floorIdArr, $floorInfoArr);
		}
		$HTML .= '<div class="comment-info clearfix"><h5 class="comment-header text-muted">';
		$HTML .= '<span class="comment-username">'.$floorInfoArr[$floorId]['user_name'].'</span>';
		$HTML .= '<span class="comment-floor pull-right">#'.$floorInfoArr[$floorId]['position'].'</span>';
		$HTML .= '</h5><div class="comment-text lead">'.$floorInfoArr[$floorId]['content'].'</div><div class="comment-action clearfix" data-floor-id="'.$floorId.'"><div class="pull-right">';
		$HTML .= '<a class="comment-up" href="#">支持(<span class="comment-up-count">'.$floorInfoArr[$floorId]['up_count'].'</span>)</a>';
		$HTML .= '<a class="comment-down" href="#">反对(<span class="comment-down-count">'.$floorInfoArr[$floorId]['down_count'].'</span>)</a>';
		$HTML .= '<a href="#" class="comment-report">举报</a><a href="#" class="comment-reply">回复</a>';
		$HTML .= '</div></div></div></blockquote>';
		return $HTML;
	}
	
	public function renderQuote2($floorIdArr, array $floorInfoArr)
	{
		$floorId = array_shift($floorIdArr);
	
		$HTML = '<blockquote class="comment-quote">';
		$HTML .= '<div class="comment-info clearfix"><h5 class="comment-header text-muted">';
		$HTML .= '<span class="comment-username">'.$floorInfoArr[$floorId]['user_name'].'</span>';
		$HTML .= '<span class="comment-floor pull-right">#'.$floorInfoArr[$floorId]['position'].'</span>';
		$HTML .= '</h5><div class="comment-text lead">'.$floorInfoArr[$floorId]['content'].'</div><div class="comment-action clearfix" data-floor-id="'.$floorId.'"><div class="pull-right">';
		$HTML .= '<a class="comment-up" href="#">支持(<span class="comment-up-count">'.$floorInfoArr[$floorId]['up_count'].'</span>)</a>';
		$HTML .= '<a class="comment-down" href="#">反对(<span class="comment-down-count">'.$floorInfoArr[$floorId]['down_count'].'</span>)</a>';
		$HTML .= '<a href="#" class="comment-report">举报</a><a href="#" class="comment-reply">回复</a>';
		$HTML .= '</div></div></div></blockquote>';
		while(!empty($floorIdArr))
		{
			$floorId = array_shift($floorIdArr);
			$HTML = '<blockquote class="comment-quote">'.$HTML;
			$HTML .= '<div class="comment-info clearfix"><h5 class="comment-header text-muted">';
			$HTML .= '<span class="comment-username">'.$floorInfoArr[$floorId]['user_name'].'</span>';
			$HTML .= '<span class="comment-floor pull-right">#'.$floorInfoArr[$floorId]['position'].'</span>';
			$HTML .= '</h5><div class="comment-text lead">'.$floorInfoArr[$floorId]['content'].'</div><div class="comment-action clearfix" data-floor-id="'.$floorId.'"><div class="pull-right">';
			$HTML .= '<a class="comment-up" href="#">支持(<span class="comment-up-count">'.$floorInfoArr[$floorId]['up_count'].'</span>)</a>';
			$HTML .= '<a class="comment-down" href="#">反对(<span class="comment-down-count">'.$floorInfoArr[$floorId]['down_count'].'</span>)</a>';
			$HTML .= '<a href="#" class="comment-report">举报</a><a href="#" class="comment-reply">回复</a>';
			$HTML .= '</div></div></div></blockquote>';
		}
		return $HTML;
	}
}

