<?php
namespace application\protect\controllers;

use framework\App as App;
use application\protect\models as models;

class BookmarkController extends CommonController
{
	public function actionAdd()
	{
		if (empty($_POST['resource_id']) || empty($_POST['type']) || !ctype_digit($_POST['resource_id']) || !ctype_digit($_POST['type']))
		{
			echo json_encode(array('code' => -1, 'msg' => '参数有误'));exit;
		}
		//检查是否已经收藏
		$userId = App::ins()->user->getId();
		$model = models\BookmarkModel::model();
		$count = $model->where("type=".$_POST['type'].",user_id=".$userId.",resource_id=".$_POST['resource_id'])->count();
		if ($count > 0)
		{
			echo json_encode(array('code' => 2, 'msg' => '已经收藏过了'));exit;
		}
		$bookmark = new models\BookmarkModel();
		$bookmark->type = $_POST['type'];
		$bookmark->user_id = $userId;
		$bookmark->resource_id = $_POST['resource_id'];
		$bookmark->add_time = $_SERVER['REQUEST_TIME'];
		$result = $bookmark->save();
		if (!empty($result))
		{
			//更新主题的收藏次数
			if ($_POST['type'] == models\BookmarkModel::BOOKMARK_TYPE_THREAD)
			{
				$sql = "UPDATE forum_thread SET bookmark_count=bookmark_count+1 WHERE id=".$_POST['resource_id'];
				$updateThread = $model->execute($sql);
				if (empty($updateThread))
				{
					echo json_encode(array('code' => 0, 'msg' => '更新收藏次数出错'));exit;
				}
			}
			
			echo json_encode(array('code' => 1, 'msg' => '收藏成功'));
		}
		else 
		{
			echo json_encode(array('code' => 0, 'msg' => '未知原因，收藏失败'));
		}
	}
}