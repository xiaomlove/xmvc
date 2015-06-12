<?php
namespace application\protect\controllers;

use framework\App as App;
use application\protect\models\TorrentModel as TorrentModel;
use framework\lib\BEncode as BEncode;
use framework;

class TorrentController extends CommonController
{
	public $layout = 'tinypt';
	
	public function actionList()
	{
		$this->setPageTitle('种子列表');
		$model = TorrentModel::model();
		$result = $model->getList($_GET);
		
		$page = !empty($_GET['page']) ? $_GET['page'] : 1;
		$per = !empty($_GET['per_page']) ? $_GET['per_page'] : 5;
		$total = ceil($result['count']/$per);
		$navHtml = $this->getNavHtml($page, $per, $total);//导航链接上的其他参数从$_GET取
		echo $this->render('torrent', array('data' => $result['data'], 'navHtml' => $navHtml));
	}
	
	public function actionDetail()
	{
		if (empty($_GET['id']) || !ctype_digit($_GET['id']))
		{
			$this->goError();
		}
		$this->setPageTitle('种子详情');
		$model = TorrentModel::model();
		$result = $model->findByPk($_GET['id'], 'id, name, main_title, slave_title, size, introduce, info_hash, view_times, download_times, finish_times, seeder_count, leecher_count, user_id, douban_id');
//		var_dump($result);exit;
		echo $this->render('detail', array('torrent' => $result));
	}
	
	public function actionUpload()
	{
		$this->setPageTitle('发布种子');
		$model = TorrentModel::model();
		$action = $this->createUrl('torrent/upload');
		if(App::ins()->request->isPost())
		{
//			echo '<pre/>';
//			var_dump($_POST);
//			var_dump($_FILES);
//			echo '<hr/>';
//			exit;
			
			if($model->validate($_POST))
			{
				if(isset($_FILES['torrentFile']) && is_uploaded_file($_FILES['torrentFile']['tmp_name']))
				{
					$uploadFile = $_FILES['torrentFile'];
					$ext = pathinfo($uploadFile['name'], PATHINFO_EXTENSION);
					if(strtolower($ext) === 'torrent')
					{
						$decode = BEncode::decode_getinfo(file_get_contents($uploadFile['tmp_name']));//取临时文件不会有中文出现
						if(!empty($decode))
						{
							$userId = App::ins()->user->getId();
// 							$userInfo = UserModel::model()->findByPk($userId, 'passkey');
// 							$passkey = $userInfo['passkey'];
//							$decode['announce'] .= '?passkey='.$passkey;//这里不需要，下载时才需要
							if (isset($decode['comment']))
							{
								$decode['comment'] = $decode['comment'].'-[come from TinyHD.net]';
							}
							else
							{
								$decode['comment'] = '[come from TinyHD.net]';
							}
							$decode['announce'] = App::ins()->request->getBaseUrl().'announce.php';//自动添加正确的地址
							$encode = BEncode::encode($decode);
							if(!empty($encode))
							{
								$torrentPath = App::getPathOfAlias(App::getConfig('torrentSavePath'));
								$torrentPath .= date('Ymd', time()).DS;//按天分目录存放
								if(!is_dir($torrentPath))
								{
									$mkdir = mkdir($torrentPath, 0777, TRUE);
									if(!$mkdir)
									{
										$model->setError('torrentFile', '种子保存目录无法创建');
										goto A;
									}
								}
								$name = date('YmdHis', time())."_{$userId}_".$uploadFile['name'];//种子文件本身也加上时间日期和用户Id
								$outPutName = $torrentPath.$name;
								if(substr(PHP_OS, 0, 3) === 'WIN')
								{
									$outPutName = mb_convert_encoding($outPutName, 'GBK', 'UTF-8,GBK,GB2312,BIG5');//windows下得转码一下？
								}
								$torrent = file_put_contents($outPutName, $encode);
								
								if($torrent === FALSE)
								{
									$model->setError('torrentFile', '生成种子错误！');
									goto A;
								}
								$info_hash = sha1(BEncode::encode($decode['info']));
								if(empty($info_hash) || strlen($info_hash) !== 40)
								{
									$model->setError('torrentFile', '获取种子info_hash出错');
									goto A;
								}
								if(isset($decode['info']['files']))
								{
									//多文件
									$fileList = serialize($decode['info']['files']);
								}
								else 
								{
									$fileList = serialize(array('length'=>$decode['info']['length'], 'name'=>$decode['info']['name']));
								}
								$sql = "INSERT INTO torrent (main_title, slave_title, info_hash, name, introduce, size, file_count, file_list, user_id, add_time, douban_id) VALUES ('{$_POST['main_title']}', '{$_POST['slave_title']}', '$info_hash', '{$name}', '{$_POST['introduce']}', {$decode['size']}, {$decode['filecount']}, '$fileList', $userId ,".time();
								if (!empty($_POST['douban_id']))
								{
									$sql .= ','.intval(trim($_POST['douban_id']));
								}
								$sql .= ')';
								$insert = $model->execute($sql);
								if($insert === 0 || $insert === FALSE)
								{
									$model->setError('torrentFile', '添加种子记录出错！');
								}							
							}
							else 
							{
								$model->setError('torrentFile', 'encode为种子文件出错');
							}
						}
						else 
						{
							$model->setError('torrentFile', 'decode种子出错！');
						}
						
					}
					else 
					{
						$model->setError('torrentFile', '确保上传的是种子,后缀为.torrent!');
					}
				}
				else
				{
					$model->setError('torrentFile', '没有上传种子文件！');
				}
			}
			A:
			$errors = $model->getError();
			if(empty($errors))
			{
				App::ins()->user->setFlash('upload_success', '发布成功！你需要重新下载种子并使用它来做种！');
				$this->redirect('torrent/detail', array('id'=>$insert));
			}
			else 
			{
				$model->setData($_POST);
				echo $this->render('upload', array('model'=>$model, 'action' => $action));//将模型传递过去，获取错误信息、字段信息以及flash之类
			}
		}
		else
		{
			echo $this->render('upload', array('model'=>$model, 'action' => $action));
		}
		
	}
	
	public function actionEdit()
	{
		$model = TorrentModel::model();
		$action = $this->createUrl('torrent/edit');
		if (App::ins()->request->isGet())
		{
			if (empty($_GET['id']) || !ctype_digit($_GET['id']))
			{
				$this->goError();
			}
			$torrent = $model->findByPk($_GET['id'], 'name,main_title,slave_title,introduce');
			$model->setData($torrent);
			$html = $this->render('upload', array('model' => $model, 'action' => $action));
			echo $html;
		}
		else 
		{
			if (empty($_POST['id']) || !ctype_digit($_POST['id']))
			{
				$this->goError();
			}
			$id = $_POST['id'];
			unset($_POST['id']);
			if ($model->validate($_POST))
			{
				$result = $model->updateByPk($id, $_POST);
				if ($result === FALSE)
				{
					$model->setError('main_title', '更新出错！');
				}
				else
				{
					App::ins()->user->setFlash('upload_success', '编辑成功！');
					$this->redirect('torrent/detail', array('id' => $id));
				}
			}
			$model->setData($_POST);
			$html = $this->render('upload', array('model' => $model));
			echo $html;
		}
	}
	
	public function actionDownload()
	{
		if (empty($_GET['id']) || !ctype_digit($_GET['id']))
		{
			$this->goError();
		}
		$model = TorrentModel::model();
		$file = $model->getTorrent($_GET['id']);
		if(!empty($file))
		{
			$encodeFile = \framework\helper\StringHelper::encodeFileName($file);
			$decode = BEncode::decode(file_get_contents($encodeFile));
			if (!empty($decode))
			{
				$userId = App::ins()->user->getId();
				$result = $model->table('user')->field('passkey')->where('id=:id', array(':id' => $userId))->limit(1)->select();
				$passkey = $result[0]['passkey'];
				$decode['announce'] .= '?passkey='.$passkey;
				$content = BEncode::encode($decode);
				if (!empty($content))
				{
					App::setConfig('noLog', TRUE);
					$sql = "UPDATE torrent SET download_times=download_times+1 WHERE id=".$_GET['id'];
					$model->execute($sql);
// 					exit('OK');
					$this->downloadFile($file, $content);
				}
				else 
				{
					die('encode出错');
				}
			}
			else 
			{
				die('decode出错');
			}
			
		}
		else
		{
			die('种子不存在！');
		}
	}
	
	public function actionGetSeederLeecher()
	{
		if (App::ins()->request->isAjax())
		{
			if (empty($_GET['id']) || !ctype_digit($_GET['id']) || 
				!isset($_GET['seederCount']) || !ctype_digit($_GET['seederCount']) || 
				!isset($_GET['leecherCount']) || !ctype_digit($_GET['leecherCount']))
			{
				echo json_encode(array('code' => -1, 'msg' => '参数错误'));exit;
			}
			$model = TorrentModel::model();
			$return = array('code' => 1);
			if ($_GET['seederCount'] > 0)
			{
				//找seeder，做种者，是要下载完成了的
				$sql = "SELECT peer.id as in_peer_id,snatch.*,user.name as username FROM peer 
						LEFT JOIN snatch ON peer.torrent_id=snatch.torrent_id AND peer.user_id=snatch.user_id 
						LEFT JOIN user ON peer.user_id=user.id 
						WHERE peer.torrent_id={$_GET['id']} AND snatch.torrent_id={$_GET['id']} AND peer.is_seeder=1 AND snatch.is_seeder=1  
						ORDER BY snatch.uploaded DESC";
				
				$seederList = $model->findBySql($sql);
				$seederCount = count($seederList);
				if ($seederCount != $_GET['seederCount'])
				{
					//不同步了，更新一下？
					$updateSeederCountSql = "UPDATE torrent SET seeder_count=$seederCount WHERE id=".$_GET['id'];
					$model->execute($updateSeederCountSql);
					$return['updateSeederCount'] = 1;
				}
				if ($seederCount > 0)
				{
					$this->_convert($seederList);
					$return['msg']['seeder']['data'] = $seederList;
					$return['msg']['seeder']['count'] = $seederCount;
				}
			}
			if ($_GET['leecherCount'] > 0)
			{
				//找leecher，下载者，没下载完成的
				$sql = "SELECT peer.id as in_peer_id,snatch.*,user.name as username FROM peer 
						LEFT JOIN snatch ON peer.torrent_id=snatch.torrent_id AND peer.user_id=snatch.user_id 
						LEFT JOIN user ON peer.user_id=user.id 
						WHERE peer.torrent_id={$_GET['id']} AND snatch.torrent_id={$_GET['id']} AND peer.is_seeder=0 AND snatch.is_seeder=0 AND snatch.complete_time=0  
						ORDER BY peer.uploaded DESC";
				
				$leecherList = $model->findBySql($sql);
				$leecherCount = count($leecherList);
				if ($leecherCount != $_GET['leecherCount'])
				{
					//不同步了，更新一下？
					$updateLeecherCountSql = "UPDATE torrent SET leecher_count=$leecherCount WHERE id=".$_GET['id'];
					$model->execute($updateLeecherCountSql);
					$return['updateLeecherCount'] = 1;
				}
				if ($leecherCount > 0)
				{
					$this->_convert($leecherList);
					$return['msg']['leecher']['data'] = $leecherList;
					$return['msg']['leecher']['count'] = $leecherCount;
				}
			}
			echo json_encode($return);
		}		
	}
	/**
	 * 转化一下数据
	 * @param array $data
	 */
	private function _convert(array &$data)
	{
		foreach ($data as &$item)
		{
			$item['connect_time'] = $this->getTTL($item['connect_time'], '', $item['connect_time']);
			$item['this_report_time'] = date('m-d H:i', $item['this_report_time']);
			$item['downloaded_converted'] = $this->getSize($item['downloaded']);
			$item['uploaded_converted'] = $this->getSize($item['uploaded']);
			$item['download_speed'] = $this->getSpeed($item['download_speed']);
			$item['upload_speed'] = $this->getSpeed($item['upload_speed']);
			
		}
	}
	
	public function actionSnatch()
	{
		if (empty($_GET['id']) || !ctype_digit($_GET['id']))
		{
			$this->goError();
		}
		$model = TorrentModel::model();
		$torrent = $model->findByPk($_GET['id'], 'id,name');
		if (empty($torrent))
		{
			$this->goError();
		}
		$sql = "SELECT snatch.*,user.name as user_name FROM snatch LEFT JOIN user ON snatch.user_id=user.id WHERE snatch.torrent_id=".$_GET['id']." AND complete_time > 0 ORDER BY snatch.complete_time DESC";
		$snatchList = $model->findBySql($sql);
		$html = $this->render('snatch', array('torrentInfo' => $torrent, 'snatchList' => $snatchList));
		echo $html;
	}
}