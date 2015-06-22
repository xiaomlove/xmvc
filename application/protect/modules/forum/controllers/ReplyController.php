<?php
namespace application\protect\modules\forum\controllers;

use framework\App as App;
use application\protect\models as models;

class ReplyController extends \application\protect\controllers\CommonController
{
	public $layout = 'tinypt';
	private $section;
	private $thread;
	
	public function init()
	{
		
		//检查必须包含section_id和thread_id
		if (App::ins()->request->isGet())
		{
			if (empty($_GET['section_id']) || !ctype_digit($_GET['section_id']) || empty($_GET['thread_id']) || !ctype_digit($_GET['thread_id']))
			{
				$this->_goError('参数有误');
			}
			$sectionId = $_GET['section_id'];
			$threadId = $_GET['thread_id'];
		}
		elseif (App::ins()->request->isPost())
		{
			if (empty($_POST['section_id']) || !ctype_digit($_POST['section_id']) || empty($_POST['thread_id']) || !ctype_digit($_POST['thread_id']))
			{
				$this->_goError('参数有误');
			}
			$sectionId = $_POST['section_id'];
			$threadId = $_POST['thread_id'];
		}
		
		$section = $this->section = models\ForumsectionModel::model()->findByPk($sectionId, 'id, name, parent_id');
		if (empty($section))
		{
			$this->_goError('版块不存在');
		}
		
		$thread = $this->thread = models\ForumthreadModel::model()->findByPk($threadId, 'id, title');
		if (empty($thread))
		{
			$this->_goError('主题不存在');
		}
		//面包屑
		if (!App::ins()->request->isAjax())
		{
			$this->breadcrumbs[] = array('name' => 'TinyHD论坛', 'url' => $this->createUrl('forum/section/list'));
			$this->breadcrumbs[] = array('name' => $section['name'], 'url' => $this->createUrl('forum/thread/list', array('section_id' => $section['id'])));
			$this->breadcrumbs[] = array('name' => $thread['title'], 'url' => $this->createUrl('forum/thread/detail', array('section_id' => $section['id'], 'thread_id' => $thread['id'])));
			$this->breadcrumbs[] = array('name' => '发表回复');
		}
	}
	
	/**
	 * 发表回复，包括快速回复和高级模式
	 * Enter description here ...
	 */
	public function actionAdd()
	{
		$model = models\ForumreplyModel::model();
		if (App::ins()->request->isPost())
		{
			if ($model->validate($_POST))
			{
				//获得当前最大层数
				$sql = "SELECT max(floor) as maxFloor FROM forum_reply WHERE section_id={$_POST['section_id']} AND thread_id={$_POST['thread_id']} AND to_user_id=0";
				$result = $model->findBySql($sql);
				if (empty($result))
				{
					$maxFloor = 0;
				}
				else
				{
					$maxFloor = $result[0]['maxFloor'];
				}
				$reply = new models\ForumreplyModel();
				$reply->content = $_POST['content'];
				$reply->user_id = App::ins()->user->getId();
				$reply->section_id = $_POST['section_id'];
				$reply->thread_id = $_POST['thread_id'];
				$reply->floor = $maxFloor+1;
				if (isset($_POST['draft']))
				{
					$reply->state = models\ForumreplyModel::STATE_DRAFT;
				}
				else
				{
					$reply->state = models\ForumreplyModel::STATE_PUBLISH;
				}
				$reply->add_time = $_SERVER['REQUEST_TIME'];
				$result = $reply->save();
				if (isset($_POST['quickReply']))
				{
					//快速回复，ajax请求
					if (empty($result))
					{
						echo json_encode(array('code' => 0, 'msg' => '未知错误，发表回复失败！'));
					}
					else 
					{
						//更新主题表的回复数以及所在版块、父版块的回复数
						$this->_updateThreadSectionUser($result, $_POST['content'], $model);
						//插入回复成功，渲染返回的html代码
						//应该加相关字段还是连一下表？？？太麻烦，还是加字段
						$userId = App::ins()->user->getId();
						$sql = "SELECT a.id,a.name,a.avatar_url,a.uploaded,a.downloaded,a.thread_count,a.reply_count,a.comment_count,b.name as roleName FROM user a LEFT JOIN role b ON b.level=a.role_level WHERE a.id=$userId";
						$userInfo = $model->findBySql($sql);
						$userInfo = $userInfo[0];
						$html = $this->renderPartial('reply', array('maxFloor' => $maxFloor, 'content' => $_POST['content'], 'userInfo' => $userInfo, 'replyId' => $result));
						echo json_encode(array('code' => 1, 'msg' => $html));
					}
					exit;
					
				}
				else
				{
					if (empty($result))
					{
						$model->setData($_POST);
						goto A;
					}
					else 
					{
						$this->_updateThreadSectionUser($result, $_POST['content'], $model);
						//跳到末页比较好，后面完善
						$this->redirect('forum/thread/detail', array('section_id' => $_POST['section_id'], 'thread_id' => $_POST['thread_id']));
					}
					
				}
			}
			else 
			{
				//没有通过验证
				if (isset($_POST['quickReply']))
				{
					echo json_encode(array('code' => 0, 'msg' => '参数未通过验证！'));
					exit;
				}
				else
				{
					$model->setData($_POST);
					goto A;	
				}
				
			}
			
		}
		A:
		echo $this->render('replyform', array('model' => $model, 'sectionId' => $this->section['id'], 'threadId' => $this->thread['id'], 'threadTitle' => $this->thread['title']));
	
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
	
	private function _updateThreadSectionUser($replyId, $content, $model)
	{
		$replyContent = mb_substr(strip_tags($content), 0, 20, 'UTF-8');//去标签，截部分
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
		$sql = "UPDATE forum_thread SET reply_count=reply_count+1,last_reply='$content',last_reply_time={$_SERVER['REQUEST_TIME']} WHERE id=".$threadId;
		$model->execute($sql);
		//更新所在版块、父版块的回复数、最近回复
		$sql = "UPDATE forum_section SET reply_total_count=reply_total_count+1,reply_today_count=reply_today_count+1,last_reply='$content' WHERE id IN ($sectionId,$parentSectionId)";
		$model->execute($sql);
		//更新用户信息
		$sql = "UPDATE user SET reply_count=reply_count+1 WHERE id=$userId";
		$model->execute($sql);
	}
	
	public function actionEdit()
	{
		$model = models\ForumreplyModel::model();
		if (App::ins()->request->isPost())
		{
			if (empty($_POST['reply_id']) || !ctype_digit($_POST['reply_id']))
			{
				$this->goError();
			}
			$result = $model->updateByPk($_POST['reply_id'], array('content' => $_POST['content']));
			if ($result !== FALSE)
			{
				$this->redirect('forum/thread/detail', array('section_id' => $_POST['section_id'], 'thread_id' => $_POST['thread_id']));
			}
			else 
			{
				$model->setData($_POST);
				$html = $this->render('replyform', array('model' => $model, 'sectionId' => $this->section['id'], 'threadId' => $this->thread['id']));
				echo $html;
			}
		}
		else
		{
			if (empty($_GET['reply_id']) || !ctype_digit($_GET['reply_id']))
			{
				$this->goError();
			}
			$userId = App::ins()->user->getId();
			$reply = $model->where("user_id=$userId AND id=".$_GET['reply_id'])->limit(1)->select();
			if (empty($reply))
			{
				$this->goError();
			}
			$reply = $reply[0];
			$reply['title'] = $this->thread['title'];
			$model->setData($reply);
			$html = $this->render('replyform', array('model' => $model, 'sectionId' => $this->section['id'], 'threadId' => $this->thread['id']));
			echo $html;
		}
	}
	
	
}