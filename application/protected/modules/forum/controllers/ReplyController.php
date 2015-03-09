<?php
class ReplyController extends CommonController
{
	public $layout = 'tinypt';
	private $section;
	private $thread;
	
	public function init()
	{
		//检查必须包含section_id和thread_id
		if (App::ins()->request->isGet())
		{
			if (!ctype_digit($_GET['section_id']) || !ctype_digit($_GET['thread_id']))
			{
				$this->_goError('参数有误');
			}
			$sectionId = $_GET['section_id'];
			$threadId = $_GET['thread_id'];
		}
		elseif (App::ins()->request->isPost())
		{
			if (!ctype_digit($_POST['section_id']) || !ctype_digit($_POST['thread_id']))
			{
				$this->_goError('参数有误');
			}
			$sectionId = $_POST['section_id'];
			$threadId = $_POST['thread_id'];
		}
		
		$section = $this->section = ForumsectionModel::model()->findByPk($sectionId, 'id, name');
		if (empty($section))
		{
			$this->_goError('版块不存在');
		}
		
		$threadId = isset($_GET['thread_id']) ? $_GET['thread_id'] : $_POST['thread_id'];
		$thread = $this->thread = ForumthreadModel::model()->findByPk($threadId, 'id, title');
		if (empty($thread))
		{
			$this->_goError('主题不存在');
		}
		//面包屑
		$this->breadcrumbs[] = array('name' => 'TinyHD论坛', 'url' => $this->createUrl('forum/section/list'));
		$this->breadcrumbs[] = array('name' => $section['name'], 'url' => $this->createUrl('forum/thread/list', array('section_id' => $section['id'])));
		$this->breadcrumbs[] = array('name' => $thread['title'], 'url' => $this->createUrl('forum/thread/detail', array('section_id' => $section['id'], 'thread_id' => $thread['id'])));
		$this->breadcrumbs[] = array('name' => '发表回复');
	}
	
	/**
	 * 发表回复，包括快速回复和高级模式
	 * Enter description here ...
	 */
	public function actionAdd()
	{
		$model = ForumreplyModel::model();
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
				$reply = new ForumreplyModel();
				$reply->content = $_POST['content'];
				$reply->user_id = App::ins()->user->getId();
				$reply->section_id = $_POST['section_id'];
				$reply->thread_id = $_POST['thread_id'];
				$reply->floor = $maxFloor+1;
				if (isset($_POST['draft']))
				{
					$reply->state = ForumreplyModel::STATE_DRAFT;
				}
				else
				{
					$reply->state = ForumreplyModel::STATE_PUBLISH;
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
						//插入回复成功，渲染返回的html代码
//						$userInfo = UserModel::model()->findByPk(App::ins()->user->getId());
						$sql = "SELECT a.name,a.uploaded,a.downloaded,b.name,count(c.*) as threadCount,count(d.*) as replyCount,count(e.*) as commentCount  
								FROM user a LEFT JOIN role b ON b.level=a.role_level 
									LEFT JOIN forum_thread c ON c.user_id=a.id 
									LEFT JOIN forum_reply d ON d.user_id=a.id 
									LEFT JOIN comment e ON e.user_id=a.id 
										WHERE a.id=".App::ins()->user->getId();
						$userInfo = $model->findBySql($sql);
						var_dump($userInfo);exit;
						$html = $this->renderPartial('reply', array('maxFloor' => $maxFloor, 'content' => $_POST['content'], 'userInfo' => $userInfo));
						echo json_encode(array('code' => 1, 'msg' => '发表回复成功！'));
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
		if (isset($_POST['quickReply']))
		{
			echo json_encode(array('code' => 0, 'msg' => $msg));
		}
		else
		{
			$this->goError();
		}
	}
}