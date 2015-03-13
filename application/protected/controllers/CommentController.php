<?php
class CommentController extends CommonController
{
	public function actionAdd()
	{
		if (App::ins()->request->isPost())
		{
			if (empty($_POST['torrentId']) || empty($_POST['comment']) || empty($_POST['floor']))
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
			$comment->floor = $_POST['floor'];
			$comment->add_time = $_SERVER['REQUEST_TIME'];
			$result = $comment->save();
			if (!empty($result))
			{
				$sql = "UPDATE torrent SET comment_count=comment_count+1 WHERE id=".$_POST['torrentId'];
				$result = TorrentModel::model()->execute($sql);
				if(!empty($result))
				{
					echo json_encode(array('code' => 1, 'msg' => '添加成功'));
				}
				else
				{
					echo json_encode(array('code' => 0, 'msg' => '添加成功，更新评论数量失败'));
				}
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
			$per = 5;//每页显示8条评论
			$offset = ($page-1)*$per;
			$model = CommentModel::model();
			$navHtml = 0;
			$total = 0;
			$count = 0;
			
			$count = $model->where('torrent_id=:torrentId', array(':torrentId' => $_GET['torrentId']))->count();
			if ($count == 0)//没有时是string类型的0，使用===时候要注意！
			{
				echo json_encode(array('code' => 0, 'msg' => '暂无评论'));exit;
			}
			$total = ceil($count/$per);
			$navHtml = $this->getAjaxNavHtml($per, $total);
			
			
			$sql = "SELECT a.*, b.name FROM comment as a LEFT JOIN user as b ON a.user_id = b.id WHERE a.torrent_id = :torrentId ORDER BY a.floor ASC LIMIT $offset, $per";
			$comments = $model->findBySql($sql, array(':torrentId' => $_GET['torrentId']));
			
			$html = $this->renderPartial('comment', array('comments' => $comments, 'navHtml' => $navHtml, 'floor' => $count, 'page' => $total));
			echo json_encode(array('code' => 1, 'msg' => $html));
		}
	}
}