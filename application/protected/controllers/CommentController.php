<?php
class CommentController extends Controller
{
	public function actionAdd()
	{
		if (App::ins()->request->isPost())
		{
			if (empty($_POST['torrentId']) || empty($_POST['comment']))
			{
				echo json_encode(array('code' => -1, 'msg' => '参数不全'));exit;
			}
			$parentId = empty($_POST['parentId']) ? 0 : $_POST['parentId'];
			if ($parentId !== 0)
			{
				$model = CommentModel::model();
				$parent = $model->findByPk($parentId, 'id, path, level');
				if (empty($parent))
				{
					echo json_encode(array('code' => 0, 'msg' => '父评论不存在！'));exit;
				}
				$path = $parent['path'].','.$_POST['torrentId'];
				$level = $parent['level']+1;
			}
			else 
			{
				$path = $_POST['torrentId'];
				$level = 1;
			}
			$comment = new CommentModel();
			$comment->parent_id = $parentId;
			$comment->user_id = App::ins()->user->getId();
			$comment->torrent_id = $_POST['torrentId'];
			$comment->path = $path;
			$comment->level = $level;
			$comment->content = $_POST['comment'];
			$result = $comment->save();
			if (!empty($result))
			{
				echo json_encode(array('code' => 1, 'msg' => '添加成功'));
			}
			else
			{
				echo json_encode(array('code' => 0, 'msg' => '添加失败'));
			}
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
			$per = 8;//每页显示8条评论
			$offset = ($page-1)*$per;
			$model = CommentModel::model();
			$comments = $model->where('torrent_id=:torrentId', array(':torrentId' => $_GET['torrentId']))->limit("$offset, $per")->select();
			if (empty($comments))
			{
				echo json_encode(array('code' => 0, 'msg' => '暂无评论'));exit;
			}
			$html = $this->renderPartial('comment', array('comments' => $comments));
			echo json_encode(array('code' => 1, 'msg' => $html));
		}
	}
}