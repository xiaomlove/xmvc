<?php
class ReplyreplyController extends CommonController
{
	public $layout = 'tinypt';
	private $section;
	private $thread;
	
	public function init()
	{
		//检查必须包含section_id和thread_id
		if (!ctype_digit($_POST['section_id']))
		{
			$this->_goError('参数有误');
		}
		$sectionId = $_POST['section_id'];
		
		if (!ctype_digit($_POST['thread_id']))
		{
			$this->_goError('参数有误');
		}
		$threadId = $_POST['thread_id'];
		
		$section = $this->section = ForumsectionModel::model()->findByPk($sectionId, 'id, name, parent_id');
		if (empty($section))
		{
			$this->_goError('版块不存在');
		}
		
		$thread = $this->thread = ForumthreadModel::model()->findByPk($threadId, 'id, title');
		if (empty($thread))
		{
			$this->_goError('主题不存在');
		}
	}
	
	/**
	 * 发表回复的回复
	 * Enter description here ...
	 */
	public function actionAdd()
	{
		$model = ForumreplyreplyModel::model();
		
		if (App::ins()->request->isPost())
		{
			if ($model->validate($_POST))
			{
				//获得当前最大层数
				$sql = "SELECT max(floor) as maxFloor FROM forum_reply_reply WHERE reply_id={$_POST['reply_id']}";
				$result = $model->findBySql($sql);
				if (empty($result))
				{
					$maxFloor = 1;
				}
				else
				{
					$maxFloor = $result[0]['maxFloor']+1;
				}
				$_POST['floor'] = $maxFloor;
				$_POST['content'] = htmlspecialchars($_POST['content']);
				$userId = App::ins()->user->getId();
				$_POST['user_id'] = $userId;
				$_POST['state'] = ForumreplyreplyModel::STATE_PUBLISH;
				$_POST['add_time'] = $_SERVER['REQUEST_TIME'];
//				var_dump($_POST);exit;
				$result = $model->insert($_POST);
				if (empty($result))
				{
					echo json_encode(array('code' => 0, 'msg' => '未知错误，发表回复失败！'));
				}
				else 
				{
					$this->_updateReplyThreadSectionUser($result, $_POST, $model);
					
				}
				
			}
			else 
			{
				echo json_encode(array('code' => 0, 'msg' => '参数未通过验证！'));
				
			}
		}
	}
	
	private function _goError($msg)
	{
		if (isset($_POST['quickReply']) || App::ins()->request->isAjax())
		{
			echo json_encode(array('code' => 0, 'msg' => $msg));
		}
		else
		{
			$this->goError();
		}
	}
	
	private function _updateReplyThreadSectionUser($replyId, $data, $model)
	{
		$replyContent = mb_substr(strip_tags($data['content']), 0, 20, 'UTF-8');//去标签，截部分
		$userId = App::ins()->user->getId();
		$userName = App::ins()->user->getName();
		$sectionId = $this->section['id'];
		$parentSectionId = $this->section['parent_id'];
		$threadId = $this->thread['id'];
		$content = serialize(array(
				'userId' => $userId,
				'userName' => $userName,
				'sectionId' => $sectionId,
				'threadId' => $threadId,
				'content' => $replyContent,
				'addTime' => $_SERVER['REQUEST_TIME'],
		));
		
		//更新主题的回复数、最近回复、最近回复时间
		$sql = "UPDATE forum_thread SET reply_count=reply_count+1,last_reply='{$data['content']}',last_reply_time={$_SERVER['REQUEST_TIME']} WHERE id=".$threadId;
		$model->execute($sql);
		//更新所在版块、父版块的回复数、最近回复
		$sql = "UPDATE forum_section SET reply_total_count=reply_total_count+1,reply_today_count=reply_today_count+1,last_reply='$content' WHERE id IN ($sectionId,$parentSectionId)";
		$model->execute($sql);
		//更新用户信息
		$sql = "UPDATE user SET reply_count=reply_count+1 WHERE id=$userId";
		$model->execute($sql);
		
		//渲染返回的html代码
		$userInfo = $model->table('user')->field('id, avatar_url, name, uploaded, downloaded')->where('id='.$userId)->limit(1)->select();
		$userInfo = $userInfo[0];
		$html = $this->renderPartial('replyreply', array('maxFloor' => $data['floor'], 'data' => $data, 'userInfo' => $userInfo));
		
		//更新父回复的回复数
		$sql = "UPDATE forum_reply SET reply_count=reply_count+1";
		if ($data['floor'] <= 5)
		{
			//前5楼子回复的html代码写入到父回复中
			$sql .= ",front_reply = concat(front_reply,'$html')";
		}
		$sql .= " WHERE id={$_POST['reply_id']}";
		$model->execute($sql);
		echo json_encode(array('code' => 1, 'msg' => $html));
	}
	
	
}