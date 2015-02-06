<?php
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
		$this->setPageTitle('种子详情');
		echo $this->render('detail');
	}
	
	public function actionUpload()
	{
		$this->setPageTitle('发布种子');
		$model = TorrentModel::model();
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
							$userInfo = UserModel::model()->findByPk($userId, 'passkey');
							$passkey = $userInfo['passkey'];
//							$decode['announce'] .= '?passkey='.$passkey;//这里不需要，下载时才需要
							$decode['comment'] = 'come from xiaomlove.com';
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
								$sql = "INSERT INTO torrent (main_title, slave_title, info_hash, name, introduce, size, file_count, file_list, user_id, add_time) VALUES ('{$_POST['main_title']}', '{$_POST['slave_title']}', '$info_hash', '{$name}', '{$_POST['introduce']}', {$decode['size']}, {$decode['filecount']}, '$fileList', $userId ,".time().")";
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
				echo $this->render('upload', array('model'=>$model));//将模型传递过去，获取错误信息、字段信息以及flash之类
			}
		}
		else
		{
			echo $this->render('upload', array('model'=>$model));
		}
		
	}
}