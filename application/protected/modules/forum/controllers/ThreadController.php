<?php
class ThreadController extends CommonController
{
	public $layout = 'tinypt';
	private $section;//存起来所在版块，方便调用
	
	public function init()
	{
		if (isset($_POST['addview']) || isset($_POST['addappraise']))
		{
			return;//添加浏览量不需要
		}
		if (isset($_GET['page']) && !ctype_digit($_GET['page']))
		{
			$this->goError();
		}
		if (isset($_GET['section_id']) && !ctype_digit($_GET['section_id']))
		{
			$this->goError();
		}
		$this->breadcrumbs[] = array('name' => 'TinyHD论坛', 'url' => $this->createUrl('forum/section/list'));
		$section = $this->section = ForumsectionModel::model()->findByPk($_GET['section_id']);
		if (empty($section))
		{
			$this->goError();
		}
		$this->breadcrumbs[] = array('name' => $section['name'], 'url' => $this->createUrl('forum/thread/list', array('section_id' => $_GET['section_id'])));
	}
	
	public function actionAdd()
	{
		$model = ForumthreadModel::model();
		if (App::ins()->request->isPost())
		{
			if ($model->validate($_POST))
			{
				$userId = App::ins()->user->getId();
				$thread = new ForumthreadModel();
				$thread->title = $_POST['title'];
				$thread->content = $_POST['content'];
				$thread->add_time = $_SERVER['REQUEST_TIME'];
				$thread->user_id = $userId;
				$thread->section_id = $_POST['section_id'];
				if (isset($_POST['draft']))
				{
					$thread->state = ForumthreadModel::STATE_DRAFT;
				}
				else
				{
					$thread->state = ForumthreadModel::STATE_PUBLISH;
				}
				$result = $thread->save();
				if (empty($result))
				{
					$model->setError('title', '未知错误，发表失败');
				}
				else 
				{
					//更新所在版块的信息
					$sectionModel = ForumsectionModel::model();
					$SectionParent = $sectionModel->findByPk($this->section['parent_id']);
					$sql = "UPDATE forum_section SET thread_total_count=thread_total_count+1, thread_today_count=thread_today_count+1 WHERE id IN ({$_POST['section_id']},{$SectionParent['id']})";
					$updateSection = $sectionModel->execute($sql);
					if (empty($updateSection))
					{
						trigger_error('更新所在版块和父版块信息出错', E_USER_ERROR);
						exit;
					}
					//更新用户信息
					$sql = "UPDATE user SET thread_count=thread_count+1 WHERE id=$userId";
					$updateUser = $sectionModel->execute($sql);
					if (empty($updateUser))
					{
						trigger_error('更新用户信息出错', E_USER_ERROR);
						exit;
					}
					$this->redirect('forum/thread/detail', array('section_id' => $_POST['section_id'], 'thread_id' => $result));
				}
			}
			$model->setData($_POST);
		}
		
		$this->breadcrumbs[] = array('name' => '发表主题');
		$html = $this->render('threadform', array('model' => $model));
		echo $html;
	}
	
	public function actionDetail()
	{
		if (!ctype_digit($_GET['thread_id']) || (isset($_GET['per_page']) && !ctype_digit($_GET['per_page'])))
		{
			$this->goError();
		}
		$thread = NULL;
		$appraiseList = array();
		$threadModel = ForumthreadModel::model();
		if (empty($_GET['page']) || $_GET['page'] < 2)
		{
			//取thread信息
			$sql = "SELECT a.*,b.name,b.role_name,b.uploaded,b.downloaded,b.thread_count,b.reply_count as user_info_reply_count,b.comment_count FROM forum_thread a,user b WHERE b.id=a.user_id AND a.id=".$_GET['thread_id'];
			$thread = $threadModel->findBySql($sql);
			if (empty($thread))
			{
				$this->goError();
			}
			$thread = $thread[0];
			// 		echo '<pre/>';
			//		var_dump($thread);
			//取appraise信息
			$sql = "SELECT a.*,b.name FROM forum_appraise a LEFT JOIN user b ON b.id=a.user_id WHERE thread_id={$_GET['thread_id']} ORDER BY id DESC";
			$appraiseList = $threadModel->findBySql($sql);
			//		var_dump($appraiseList);
			
		}
		
		//取reply信息
// 		$sql = "select aa.*,bb.name,bb.role_name,bb.uploaded,bb.downloaded,bb.thread_count,bb.reply_count as user_info_reply_count,bb.comment_count FROM forum_reply aa LEFT JOIN user bb ON aa.user_id=bb.id WHERE aa.thread_id=".$_GET['thread_id'];
		$data = $threadModel->getReplyList($_GET);
// 		var_dump($replyList);exit;
		$replyList = $data['data'];
		//分页代码及返回链接
		$page = !empty($_GET['page']) ? $_GET['page'] : 1;
		$per = $data['per_page'];
		$total = ceil($data['count']/$per);
		$backUrl = $this->createUrl('forum/thread/list', array('section_id' => $this->section['id']));
		$referer = App::ins()->request->getReferer();
		if (stripos($referer, $backUrl) !== FALSE)
		{
			$backUrl = $referer;
		}
		$prepend = "<li><a href=\"".$backUrl."\"><span class=\"glyphicon glyphicon-arrow-left\" aria-hidden=\"true\"></span>返回</a></li>";
		$navHtml = $this->getNavHtml($page, $per, $total, $prepend);//导航链接上的其他参数从$_GET取
		
		$html = $this->render('threaddetail', array('section' => $this->section, 'thread' => $thread, 'replyList' => $replyList, 'appraiseList' => $appraiseList, 'navHtml' => $navHtml));
		echo $html;
	}
	
	public function actionList()
	{
		$model = ForumthreadModel::model();
		$data = $model->getThreadList($_GET);
		$threadList = $data['data'];
		$per = $data['per_page'];
		$total = ceil($data['count']/$per);
		$page = !empty($_GET['page']) ? $_GET['page'] : 1;
		$prepend = "<li><a href=\"".$this->createUrl('forum/section/list')."\"><span class=\"glyphicon glyphicon-arrow-left\" aria-hidden=\"true\"></span>返回</a></li>";
		$navHtml = $this->getNavHtml($page, $per, $total, $prepend);
		$html = $this->render('threadlist', array('threadList' => $threadList, 'sectionId' => $_GET['section_id'], 'navHtml' => $navHtml));
		echo $html;
	}
	
	public function actionAddview()
	{
		if (!isset($_POST['thread_id']) || !ctype_digit($_POST['thread_id']))
		{
			echo json_encode(array('code' => -1, 'msg' => '参数不全'));
			exit;
		}
		$model = ForumthreadModel::model();
		$sql = "UPDATE forum_thread SET view_count=view_count+1 WHERE id=".$_POST['thread_id'];
		$result = $model->execute($sql);
		if (!empty($result))
		{
			echo json_encode(array('code' => 1, 'msg' => '增加成功'));
		}
		else
		{
			echo json_encode(array('code' => 0, 'msg' => '没有变化'));
		}
	}
	
	public function actionAddappraise()
	{
		if (empty($_POST['thread_id']) || !ctype_digit($_POST['thread_id']))
		{
			echo json_encode(array('code' => -1, 'msg' => '参数不合法'));exit;
		}
		$model = ForumappraiseModel::model();
		if ($model->validate($_POST))
		{
			//检查是否已经支持过
			$userId = App::ins()->user->getId();
			$userName = App::ins()->user->getName();
			$count = $model->where('thread_id='.$_POST['thread_id'].',user_id='.$userId)->count();
			if ($count > 0)
			{
				echo json_encode(array('code' => 0, 'msg' => '已经支持过了'));exit;
			}
			//如果是从自己魔力里扣，检查是否足够
			if ($_POST['type'] === 'self')
			{
				$sql = "SELECT bonus FROM user WHERE id=$userId";
				$result = $model->findBySql($sql);
				if (!empty($result))
				{
					$userBonus = $result[0]['bonus'];
					if ($userBonus < $_POST['bonus'])
					{
						echo json_encode(array('code' => 0, 'msg' => '你的魔力为'.$userBonus,'，不足以扣除'));exit;
					}
				}
				else
				{
					echo json_encode(array('code' => 0, 'msg' => '你的用户账号信息异常'));exit;
				}
			}
			$appraise = new ForumappraiseModel();
			$appraise->thread_id = $_POST['thread_id'];
			$appraise->user_id = $userId;
			$appraise->reason = $_POST['reason'];
			$appraise->award_type = 1;
			$appraise->award_value = $_POST['bonus'];
			$appraise->is_good = 1;
			$result = $appraise->save();
			if (!empty($result))
			{
				//扣除用户魔力
				if ($_POST['type'] === 'self')
				{
					$sql = "UPDATE user SET bonus=bonus-".intval($_POST['bonus'])." WHERE id=$userId";
					$result = $model->execute($sql);
					if (empty($result))
					{
						echo json_encode(array('code' => 0, 'msg' => '用户魔力扣除失败'));exit;
					}
				}
				$return = $this->renderPartial('appraise', array('userName' => $userName, 'isFirst' => $_POST['isFirst'], 'data' => $_POST));
				echo json_encode(array('code' => 1, 'msg' => $return));exit;
			}
			else 
			{
				echo json_encode(array('code' => 0, 'msg' => '未知原因，支持失败'));exit;
			}
		}
		else
		{
			echo json_encode(array('code' => 0, 'msg' => '没有通过验证'));
		}
		
	}
	
}