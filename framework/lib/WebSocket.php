<?php
$host = '127.0.0.1';
$port = 2222;
$maxClient = 1000;
const MSG_TYPE_HANDSHAKE = 0;//握住信息
const MSG_TYPE_MESSAGE = 1;//正常聊天信息
const MSG_TYPE_DISCONNECT = -1;//退出信息
const MSG_TYPE_JOIN = 2;//请求加入信息，给特定用户
const MSG_TYPE_LOGIN = 3;//加入聊天信息，给全体发

$master = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);//注意不是SQL
if ($master === FALSE)
{
	echo 'socket_create() failed:'.socket_strerror(socket_last_error());
	exit();
}
socket_set_option($master, SOL_SOCKET, SO_REUSEADDR, 1);//一个端口释放可立即使用
$bind = socket_bind($master, $host, $port);
if ($bind === FALSE)
{
	echo 'socket_bind() failed:'.socket_strerror(socket_last_error());
	exit();
}
$listen = socket_listen($master, $maxClient);//超过最大监听数会有WSAECONNREFUSED错误
if ($listen === FALSE)
{
	echo 'socket_listen() failed:'.socket_strerror(socket_last_error());
	exit();
}

$clients = array();//负责用户通信的socket列表
$users = array();//用户信息
//开始循环
while(1)
{
	$sockets = $clients;//所有的socket，用于监听哪些有状态变化
	$sockets[] = $master;//包括监听主机端口的这个
	/**
	 * socket_select($sockets, $write, $except, $tv_sec)，关键函数
	 * $sockets, 所有要监听的socket组成的数组，监听里边哪些有状态变化，包括有新消息发来或者退出信息
	 * $write,不好理解，一般为NULL
	 * $except,排除参数1中的某些不监听
	 * $tv_sec，网说为0立即结束，不好理解，为正整数表示最多阻塞这么多秒，为NULL表示直到有状态变化才继续，否则一直阻塞
	 */
	$write = NULL;//函数参数是传递引用，必须定义变量
	$except = NULL;
	$tv_sec = NULL;
	socket_select($sockets, $write, $except, $tv_sec);//多路选择，监听哪些socket有状态变化，返回时将有状态变化的保留在$sockets中，其他都删除之！
	//循环有状态变化的socket
	foreach ($sockets as $socket)
	{
		if ($socket === $master)
		{
			//监听主机端口的socket有状太变化，说明有新用户接入
			$client = socket_accept($master);//创建新socket负责该用户通信
			if ($client === FALSE)
			{
				echo 'socket_accept() failed:'.socket_strerror(socket_last_error());
			}
			else
			{
				$clients[] = $client;//加入用户列表
				doHandshake($client);//进行握手
				socket_getpeername($client, $ip);//获取用户IP地址
				$response = frameEncode(json_encode(array('type' => MSG_TYPE_HANDSHAKE, 'msg' => $ip.' connected')));//编码数据帧
				sendMessage($response);
				echo "new connected $ip\r\n";
			}
		}
		else
		{
			//其他socket的状态变化
			$bytes = socket_recv($client, $buf, 1024, 0);//读取发送过来的信息的字节数
			echo "receive bytes:\r\n";
			var_dump($bytes);
			echo "receive buf:\r\n";
			var_dump($buf);
			if ($bytes === FALSE)
			{
				echo 'socket_recv() failed:'.socket_strerror(socket_last_error());
			}
			elseif($bytes == 0)
			{
				//没有内容，是断开连接         这里有问题！！！断开时并不是字节为0！！！！！！！！！！！！！！！！！！！！！！！
				socket_getpeername($client, $ip);//获取用户IP地址
				$response = frameEncode(json_encode(array('type' => MSG_TYPE_DISCONNECT, 'msg' => $ip.' disconnect')));
				sendMessage($response);
				$index = array_search($client, $clients);//寻找该socket在用户列表中的位置
				unset($clients[$index]);//删除用户
				unset($users[$index]);
				echo "user $ip disconnect\r\n";
			}
			else
			{
				//正常聊天信息
				$data = json_decode(frameDecode($buf));
				if ($data->type == MSG_TYPE_JOIN)
				{
					//握手成功请求加入
					$index = array_search($client, $clients);
					$users[$index] = $data->userinfo;//记录用户信息，含id的用户名的json字符串
					sendUserList($client, $data->userinfo);//发送用户列表
					echo "ask to join in {$data->userinfo}\r\n";
				}
				elseif($data->type == MSG_TYPE_MESSAGE)
				{
					$time = date('Y-m-d H:i:s', time());
					$response = frameEncode(json_encode(array('type' => MSG_TYPE_MESSAGE, 'msg' => $data->msg, 'time' => $time)));
					sendMessage($response);
					echo "receive message {$data->msg}\r\n";
				}
			}
		}
	}
	
}
/**
 * 握手操作
 * Enter description here ...
 * @param unknown_type $client
 */
function doHandshake($client)
{
	$header = socket_read($client, 1024);//读取头信息
	if (preg_match("/Sec-WebSocket-Key: (.*)\r\n/", $header, $match))//冒号后面有个空格
	{
		$secKey = $match[1];
		$secAccept = base64_encode(sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11', TRUE));//握手算法固定的
		$upgrade  = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
		"Upgrade: websocket\r\n" .
		"Connection: Upgrade\r\n" .
		"Sec-WebSocket-Accept:$secAccept\r\n\r\n";
		socket_write($client, $upgrade, strlen($upgrade));
	}
}

/**
 * 编码数据帧
 * Enter description here ...
 * @param unknown_type $text
 */
function frameEncode($text)
{
	$b1 = 0x80 | (0x1 & 0x0f);
	$length = strlen($text);
	
	if($length <= 125)
	{
		$header = pack('CC', $b1, $length);
	}
	elseif($length > 125 && $length < 65536)
	{
		$header = pack('CCn', $b1, 126, $length);
	}
	elseif($length >= 65536)
	{
		$header = pack('CCNN', $b1, 127, $length);
	}
	return $header.$text;
}

/**
 * 解码数据帧
 * Enter description here ...
 * @param unknown_type $text
 */
function frameDecode($text) {
	$length = ord($text[1]) & 127;
	if($length == 126) 
	{
		$masks = substr($text, 4, 4);
		$data = substr($text, 8);
	}
	elseif($length == 127) 
	{
		$masks = substr($text, 10, 4);
		$data = substr($text, 14);
	}
	else 
	{
		$masks = substr($text, 2, 4);
		$data = substr($text, 6);
	}
	$text = "";
	for ($i = 0; $i < strlen($data); ++$i) 
	{
		$text .= $data[$i] ^ $masks[$i%4];
	}
	return $text;
}

/**
 * 发送信息
 * Enter description here ...
 * @param unknown_type $msg
 */
function sendMessage($msg)
{
	global $clients;
	foreach ($clients as $client)
	{
		socket_write($client, $msg, strlen($msg));
	}
}

/**
 * 给某用户发送在线用户列表
 * Enter description here ...
 * @param unknown_type $client
 */
function sendUserList($client, $userinfo)
{
	global $users;
	$userList = json_encode($users);
	$time = date('Y-m-d H:i:s', time());
	$response = frameEncode(json_encode(array('type' => MSG_TYPE_JOIN, 'msg' => $userList, 'time' => $time, 'count' => count($users))));
	socket_write($client, $response, strlen($response));//给特定用户发送在线用户列表
	echo "send user list \r\n";
	//通知其他用户有新用户登陆
	sendMessage(frameEncode(json_encode(array('type' => MSG_TYPE_LOGIN, 'msg' => $userinfo, 'time' => $time))));
	echo "login in success\r\n";
}