<?php
namespace application\protect\controllers;

use framework\App;
use application\protect\models\TorrentModel;
use application\protect\models\CategoryModel;
use framework\lib\BEncode;
use application\protect\models\AwardModel;

class TorrentController extends CommonController
{
	public $layout = 'tinypt';
	
	public function actionList()
	{
		$this->setPageTitle('种子列表');
		$model = TorrentModel::model();
		$result = $model->getList($_GET);
// 		var_dump($result);exit;
		$page = $result['page'];
		$per = $result['per'];
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
		$result = $model->findByPk($_GET['id'], 'id, name, main_title, slave_title, size, introduce, info_hash, view_times, download_times, finish_times, seeder_count, leecher_count, file_list, file_count, user_id, douban_id, resource_type, resource_medium, imdb_rate, video_encode, audio_encode, resolution, tag, team, year, region');
		if (empty($result))
		{
			$this->goError('种子不存在！');
		}
		$CategoryModel = CategoryModel::model();
// 		echo '<pre/>';
// 		var_dump(unserialize($result['file_list']));exit;
		$fileListTable = '';
		if (!empty($result['file_list']) && ($fileListData = unserialize($result['file_list'])))
		{
			$fileListTable .= "<div class=\"file-list-table\"><table class=\"table table-hover\"><thead><tr><th>路径</th><th>尺寸</th></tr></thead><tbody>";
			if (count($fileListData) === count($fileListData, TRUE))
			{
				//一维数组，只有一个文件
				$fileListTable .= "<tr><td>".$fileListData['name']."</td><td>".$this->getSize($fileListData['length'])."</td></tr>";
			}
			else
			{
				//多个文件
				foreach ($fileListData as $file)
				{
					$path = implode('/', $file['path']);
					$fileListTable .= "<tr><td>".$path."</td><td>".$this->getSize($file['length'])."</td></tr>";
				}
			}
			$fileListTable .= "</tbody></table></div>";
		}
		//支持情况
		$awardModel = AwardModel::model();
		$awardList = $awardModel->getList($_GET['id']);
		$userAward = !empty($awardList[AwardModel::TYPE_USER]) ? $this->createAwardUserList($awardList[AwardModel::TYPE_USER]) : NULL;
		$systemAward = !empty($awardList[AwardModel::TYPE_SYSTEM]) ? $this->createAwardUserList($awardList[AwardModel::TYPE_SYSTEM]) : NULL;
		echo $this->render('detail', array('torrent' => $result, 'fileList' => $fileListTable, 'userAward' => $userAward, 'systemAward' => $systemAward, 'userAwardSum' => $awardList['sum']));
	}
	
	protected function createAwardUserList(array $data)
	{
		$result = '';
		$userId = App::ins()->user->getId();
		foreach ($data as $value)
		{
			$className = '';
			if ($value['user_id'] == $userId)
			{
				$className = "bg-danger";
			}
			$result .= '<a href="#" class="'.$className.'">'.$value['user_name'].'</a>';
		}
		return $result;
	}
	
	public function getCategory($parentKey, $subValue)
	{
		$allCategory = CategoryModel::model()->getParentSubTree();
// 		echo '<pre/>';
// 		var_dump($allCategory);exit;
		$subs = array();
		$result = '';
		if (!empty($allCategory) && is_array($allCategory))
		{
			foreach ($allCategory as $category)
			{
				if ($category['value'] == $parentKey)
				{
					$subs = $category['subs'];
					break;
				}
			}
		}
		if (!empty($subs) && is_array($subs))
		{
			foreach ($subs as $sub)
			{
				if ($sub['value'] == $subValue)
				{
					$result = $sub['name'];
					break;
				}
			}
		}
		return $result;
	}
	
	public function actionUpload()
	{
		$this->setPageTitle('发布种子');
		$model = TorrentModel::model();
		$action = $this->createUrl('torrent/upload');
		if(App::ins()->request->isPost())
		{
// 			echo '<pre/>';
// 			var_dump($_POST);
//			var_dump($_FILES);
//			echo '<hr/>';
// 			exit;
			$_POST = array_map('trim', $_POST);//去除空格
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
								if (empty($_POST['douban_id']))
								{
									$_POST['douban_id'] = 0;
								}
								if (empty($_POST['imdb_id']))
								{
									$_POST['imdb_id'] = 0;
								}
								if (empty($_POST['mtime_id']))
								{
									$_POST['mtime_id'] = 0;
								}
								if (empty($_POST['main_title']))
								{
									$_POST['main_title'] = $uploadFile['name'];
								}
								$sql = "INSERT INTO torrent (
								main_title, slave_title, info_hash, name, introduce, size, file_count, file_list, user_id, add_time, douban_id, imdb_id, mtime_id, resource_medium, resource_type, video_encode, audio_encode, imdb_rate, resolution, tag, team, year, region) VALUES (
								'{$_POST['main_title']}', '{$_POST['slave_title']}', '$info_hash', '{$name}', '{$_POST['introduce']}', {$decode['size']}, {$decode['filecount']}, '$fileList', $userId ,".TIME_NOW.", {$_POST['douban_id']}, {$_POST['imdb_id']}, {$_POST['mtime_id']}, {$_POST['resource_medium']}, {$_POST['resource_type']}, {$_POST['video_encode']}, {$_POST['audio_encode']}, {$_POST['imdb_rate']}, {$_POST['resolution']}, {$_POST['tag']}, {$_POST['team']}, {$_POST['year']}, {$_POST['region']})";
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
// 			echo '<pre/>';
// 			var_dump($errors);
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
			$torrent = $model->findByPk($_GET['id'], 'name,main_title,slave_title,introduce,douban_id,imdb_id,mtime_id,resource_type,resource_medium,imdb_rate,video_encode,audio_encode,resolution,team,year,region,tag');
			if (empty($torrent))
			{
				$this->goError('种子不存在!');
			}
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
	
	protected function createCategoryFormGroup($model)
	{
		$tree = CategoryModel::model()->getParentSubTree();//不要搞乱了model，这是CategoryModel里边的方法
// 		var_dump($model->getError());exit;
		$out = "";
		if (!empty($tree))
		{
			foreach ($tree as $item)
			{
				$out .= '<div class="form-group'.($model->hasError($item['value']) ? " has-error" : "").'">';
				$out .= '<label for="'.$item['value'].'" class="col-sm-2 control-label">'.$item['name'].'</label>';
				$out .= '<div class="col-sm-10">';
				$out .= '<select class="form-control" id="'.$item['value'].'" name="'.$item['value'].'">';
				if (!empty($item['subs']))
				{
					if ($item['value'] == 'tag')
					{
						$out .= '<option value="0">不符合请不要选...</option>';
					}
					else 
					{
						$out .= '<option value="0">务必选择正确的一项...</option>';
					}
					foreach ($item['subs'] as $sub)
					{
						$selected = "";
						if ($sub['value'] == $model->getData($item['value']))
						{
							$selected = " selected";
						}
						$out .= '<option value="'.$sub['value'].'"'.$selected.'>'.$sub['name'].'</option>';
					}
				}
				$out .= '</select>';
		        if ($model->hasError($item['value']))
		        {
		        	$out .= '<span class="help-block">'.$model->getError($item['value']).'</span>';
		        }
		        $out .= '</div></div>';
			}
		}
		return $out;
	}
	
	protected function getSearchBox()
	{
		return CategoryModel::model()->createSearchBox();
	}
	
	public function actionAddAward()
	{
		if (!App::ins()->request->isPost())
		{
			$this->goError('请求错误');
		}
		if (empty($_POST['torrentId']) || !ctype_digit($_POST['torrentId']) || 
			empty($_POST['value']) || !ctype_digit($_POST['value']) || 
			empty($_POST['type']) || !ctype_digit($_POST['type']))
		{
			$this->goError('参数错误');
		}
		
		//检查种子是否存在
		$torrentInfo = TorrentModel::model()->findByPk($_POST['torrentId']);
		if (empty($torrentInfo))
		{
			$this->goError('种子不存在');
		}
		//检查值与类型是否正确
		$model = AwardModel::model();
		$typeValueOK = $model->checkTypeValue($_POST['type'], $_POST['value']);
		if (!$typeValueOK)
		{
			$this->goError('类型与值错误');
		}
		//检查是否已经奖励过
		$userId = App::ins()->user->getId();
		$map = array(
				'torrent_id' => $_POST['torrentId'],
				'type' => $_POST['type'],
				'user_id' => $userId,
		);
		$myAward = $model->where($map)->count();
		if ($myAward > 0) 
		{
			echo json_encode(array('code' => 0, 'msg' => '已经操作过了'));exit;
		}
		$map['add_time'] = TIME_NOW;
		$map['value'] = $_POST['value'];
		$add = $model->insert($map);
		//删除用户魔力与给用户增加魔力暂时略
		if ($add)
		{
			$userName = App::ins()->user->getName();
			echo json_encode(array('code' => 1, 'msg' => '操作成功', 'data' => array('name' => $userName, 'id' => $userId, 'value' => $_POST['value'])));
		}
		else
		{
			echo json_encode(array('code' => -1, 'msg' => '操作失败'));
		}
	}
}