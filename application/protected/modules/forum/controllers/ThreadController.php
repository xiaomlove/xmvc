<?php
class ThreadController extends CommonController
{
	public $layout = 'tinypt';
	private $section;//存起来所在版块，方便调用
	
	public function init()
	{
		if (!ctype_digit($_GET['section_id']) && !ctype_digit($_POST['section_id']))
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
				$thread = new ForumthreadModel();
				$thread->title = $_POST['title'];
				$thread->content = $_POST['content'];
				$thread->add_time = $_SERVER['REQUEST_TIME'];
				$thread->user_id = App::ins()->user->getId();
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
					$update = $sectionModel->execute($sql);
					if (empty($update))
					{
						trigger_error('更新所在版块和父版块信息出错', E_USER_ERROR);
						exit;
					}
					$this->redirect('forum/thread/detail', array('section_id' => $_POST['section_id'], 'id' => $result));
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
		if (!ctype_digit($_GET['thread_id']))
		{
			$this->goError();
		}
		//取thread信息
		
		$threadModel = ForumthreadModel::model();
		$sql = "SELECT a.*,b.name,b.role_name,b.uploaded,b.downloaded,b.thread_count,b.reply_count as user_info_reply_count,b.comment_count FROM forum_thread a,user b WHERE b.id=a.user_id AND a.id=".$_GET['thread_id'];
		$thread = $threadModel->findBySql($sql);
		if (empty($thread))
		{
			$this->goError();
		}
		$thread = $thread[0];
//		echo '<pre/>';
//		var_dump($thread);
		//取appraise信息
		$sql = "SELECT a.*,b.name FROM forum_appraise a LEFT JOIN user b ON b.id=a.user_id WHERE thread_id={$_GET['thread_id']} ORDER BY id DESC";
		$appraiseList = $threadModel->findBySql($sql);
//		var_dump($appraiseList);
		//取reply信息
		$sql = "select aa.*,bb.name,bb.role_name,bb.uploaded,bb.downloaded,bb.thread_count,bb.reply_count as user_info_reply_count,bb.comment_count FROM (select b.* FROM forum_reply a LEFT JOIN forum_reply b ON b.thread_id=a.id OR b.id=a.id WHERE a.thread_id={$thread['id']} AND b.floor < 6 ORDER BY a.floor ASC,b.floor ASC) aa INNER JOIN user bb ON aa.user_id=bb.id";
		$replyList = $threadModel->findBySql($sql);
		if (!empty($replyList))
		{
			foreach ($replyList as &$reply)
			{
				if ($reply['reply_id'] == 0)
				{
					$replyList[$key]['children']
				}
			}
		}
//		var_dump($replyList);exit;
		$html = $this->render('threaddetail', array('section' => $this->section, 'thread' => $thread, 'replyList' => $replyList, 'appraiseList' => $appraiseList));
		echo $html;
	}
	
	public function actionList()
	{
		$model = ForumthreadModel::model();
		$sql = "SELECT a.*,b.name as user_name,c.name as last_user_name FROM forum_thread a LEFT JOIN user b ON a.user_id=b.id LEFT JOIN user c ON a.last_user_id=c.id WHERE section_id={$_GET['section_id']} ORDER BY a.add_time DESC";
		$threadList = $model->findBySql($sql);
		$html = $this->render('threadlist', array('threadList' => $threadList, 'sectionId' => $_GET['section_id']));
		echo $html;
	}
}