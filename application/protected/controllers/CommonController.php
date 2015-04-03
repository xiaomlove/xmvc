<?php
class CommonController extends Controller
{
	public $breadcrumbs = array();//面包屑导航
	public $admitActions = array();//免权限检查的action
	
	public function __construct()
	{
		//$this->checkLogin();
		$this->checkHasRule();
		parent::__construct();
	}
	/**
	 * 这种限制要登陆才能访问一般怎么来？？
	 */
	private function checkLogin()
	{
		if((CONTROLLER === 'Index' && ACTION === 'Home') || CONTROLLER !== 'Index')
		{
			$isLogin = App::ins()->user->isLogin();
			if(!$isLogin)
			{
				$this->redirect('index/login');
			}
		}
	}
	
	private function checkHasRule()
	{
		if (App::ins()->user->getId() == 11)
		{
			return TRUE;//超级管理员
		}
		if (!empty($this->admitActions))//查看是否为不用权限验证的action
		{
			$admit = array_flip($this->admitActions);
			$admit = array_change_key_case($admit);
			$admit = array_flip($admit);
			if (in_array(strtolower(ACTION), $admit))
			{
				return TRUE;
			}
		}
		if (MODULE == NULL)
		{
			$ruleMvc = strtolower('null/'.CONTROLLER.'/'.ACTION);
		}
		else
		{
			$ruleMvc = strtolower(MODULE.'/'.CONTROLLER.'/'.ACTION);
		}
		$hasRule = RuleModel::model()->initRule($ruleMvc);
// 		return TRUE;
		if (!$hasRule)
		{
			if (App::ins()->request->isAjax())
			{
				if (!App::ins()->user->isLogin())
				{
					echo json_encode(array('code' => -99, 'msg' => '请先登陆'));exit;
				}
				else
				{
					echo json_encode(array('code' => -100, 'msg' => '没有该权限'));exit;
				}
				
			}
			else
			{
//				if (!App::ins()->user->isLogin())
//				{
//					$this->redirect('index/login');
//				}
//				else
//				{
					die('没有该权限');
//				}
			}
		}
	}
	
	protected function goError()
	{
		$this->redirect('index/error');exit;
	}
	/**
	 * 返回过去某个时间点离现在时间过去了多久
	 * @param string||int $time 过去某个时间点
	 * @param string $delimiter 各个时间值之间的分割符，如小时与分钟中间用一个换行分割
	 * @param string $d 已经过去了的时间，如果有提供只是转换该值，否则通过当前时间与参数一相减获得
	 * @return NULL|string
	 */
	protected function getTTL($time, $delimiter = '<br/>', $d = '')
	{
		if(empty($time))
		{
			return NULL;
		}
		if(!ctype_digit($time))
		{
			trigger_error('getTTL()只接收时间戳', E_USER_NOTICE);
		}
		if ($d === '')
		{
			$d = time() - intval($time);//过去了这么多秒
		}
		$out = '';
		if($d <= 3600)//小于1小时
		{
			$part1 = floor($d/60);//分钟
			$part2 = $d - $part1 * 60;
			$out = $part1.'分钟'.$delimiter.$part2.'秒';
		}
		elseif($d > 3600 && $d <= 3600 * 24)//小于1天
		{
			$part1 = floor($d/3600);//小时
			$part2 = $d - $part1 * 3600;
			$out = $part1.'小时'.$delimiter.round($part2/60).'分钟';
		}
		elseif($d > 3600 * 24 && $d <= 3600 * 24 * 30)//小于一个月
		{
			$part1 = floor($d/3600/24);//天
			$part2 = $d - $part1 * 3600 * 24;
			$out = $part1.'天'.$delimiter.round($part2/3600).'小时';
		}
		elseif($d > 3600 * 24 * 30 && $d <= 3600 * 24 * 365)
		{
			$part1 = floor($d/3600/24/30);//月
			$part2 = $d - $part1 * 3600 * 24 * 30;
			$out = $part1.'月'.$delimiter.round($part2/3600/24).'天';
		}
		elseif($d > 3600 * 24 * 365)
		{
			$part1 = floor($d/3600/24/365);//年
			$part2 = $d - $part1 * 3600 * 24 * 365;
			$out = $part1.'年'.$delimiter.round($part2/3600/24/30).'月';
		}
		return $out;
	}
	
	protected function getSize($size)
	{
		if(empty($size))
		{
			return 0;
		}
		if(!ctype_digit($size))
		{
			trigger_error('getSize()只接收正整数', E_USER_NOTICE);
		}
		if($size <= 1024)//不足1KB
		{
			return number_format($size/1024, 2).'KB';
		}
		elseif($size > 1024 && $size <= 1024 * 1024)//不足1MB
		{
			return number_format($size/1024, 0).'KB';
		}
		elseif($size > 1024 * 1024 && $size <= 1024 * 1024 * 1024)//不足1GB
		{
			return number_format($size/1024/1024, 1).'MB';
		}
		elseif($size > 1024 * 1024 * 1024)//大于1GB
		{
			return number_format($size/1024/1024/1024, 2).'GB';
		}
	}
	
	protected function getSpeed($speed)
	{
		if (empty($speed))
		{
			return 0;
		}
		elseif ($speed/1024 < 1024)
		{
			return number_format($speed/1024, 0).'KB/S';
		}
		elseif ($speed/1024 >= 1024)
		{
			return number_format($speed/1024/1024, 2).'MB/S';
		}
	}
	/**
	 * 返回分页的HTML代码
	 * @param unknown_type $page 当前活动页
	 * @param unknown_type $per 每页显示条数
	 * @param unknown_type $total 总页数
	 * @param string $prepend 导航前面需要插入的项
	 * @param string $append 导航后面需要插入的项
	 */
	protected function getNavHtml($page = 1, $per = 10, $total, $prepend = '', $append = '')
	{
		$url = self::getNavHref();

		$HTML = '<ul class="pagination">';
		if (!empty($prepend))
		{
			$HTML .= $prepend;
		}
// 		var_dump($total);exit;
		if ($total < 2)
		{
			goto A;
		}
		if ($total <= 10)//总页数少于10页
		{
			//拼凑上一页
			if($page == 1)
			{
				$HTML .= '<li class="disabled"><a><span aria-hidden="true">&laquo;</span></a></li>';
			}
			else 
			{
				$HTML .= '<li><a href="'.$url."page=".($page-1).'"><span aria-hidden="true">&laquo;</span></a></li>';
			}
			for ($i = 1; $i <= $total; $i++)
			{
				$class = $i == $page ? ' class="active"' : '';
				
				$HTML .= '<li'.$class.'><a href="'.$url.'page='.$i.'"><span aria-hidden="true">'.$i.'</span></a></li>';
			}
			if($page == $total)
			{
				$HTML .= '<li class="disabled"><a><span aria-hidden="true">&raquo;</span></a></li>';
			}
			else 
			{
				$HTML .= '<li><a href="'.$url."page=".($page+1).'"><span aria-hidden="true">&raquo;</span></a></li>';
			}
		}
		else
		{
			die('超过10页暂时不考虑');
		}
		
		A:
		if (!empty($append))
		{
			$HTML .= $append;
		}
		$HTML .= '</ul>';
		return $HTML;
	}
	
	private function getNavHref()
	{
		$urlQuery = $_SERVER['QUERY_STRING'];
		if(!empty($urlQuery))
		{
			parse_str($urlQuery, $urlQueryArr);
			unset($urlQueryArr['page']);
			$queryStr = http_build_query($urlQueryArr);
		}
		$baseUrl = $this->getMVCUrl();
		
		if(!empty($queryStr))
		{
			return $baseUrl.'?'.$queryStr.'&';
		}
		else
		{
			return $baseUrl.'?';
		}
	}
	
	protected function getSortHref($field, $text)
	{
		if (empty($field) || !is_string($field))
		{
			return NULL;
		}
		$field = trim($field);
		$urlQuery = $_SERVER['QUERY_STRING'];
		if (!empty($urlQuery))
		{
			parse_str($urlQuery, $urlQueryArr);
			if (isset($urlQueryArr['sort_field']) && $urlQueryArr['sort_field'] === $field)
			{
				if (isset($urlQueryArr['sort_type']) && strtolower($urlQueryArr['sort_type']) === 'asc')
				{
					$urlQueryArr['sort_type'] = 'desc';
					$direction = 'up';
				}
				else 
				{
					$urlQueryArr['sort_type'] = 'asc';
					$direction = 'down';
				}
			}
			else
			{
				$urlQueryArr['sort_field'] = $field;
				$urlQueryArr['sort_type'] = 'desc';
				$direction = 'up';
			}
			unset($urlQueryArr['page']);
			$queryStr = http_build_query($urlQueryArr);
		}
		else
		{
			$queryStr = 'sort_field='.$field.'&sort_type=desc';
			$direction = 'up';
		}
		$baseUrl = $this->getMVCUrl();
		$href = $baseUrl.'?'.$queryStr;
		$icon = '';
		if (isset($_GET['sort_field']) && $field === $_GET['sort_field'])
		{
			$icon = "<span class='glyphicon glyphicon-arrow-$direction' aria-hidden='true'></span>";
		}
		return "<a href='$href'>{$text}{$icon}</a>";
	}
	
	protected function getTorrentName($name)
	{
		if (empty($name) || !is_string($name))
		{
			return NULL;
		}
		$name = trim($name);
		if(preg_match('/(\d){14}_(\d)+_/', $name, $match))
		{
			return str_replace($match[0], '', $name);
		}
		else
		{
			return $name;
		}
	}
	
	protected function downloadFile($file, $content = '')
	{
		$filename = basename($file);
		$filename  = self::getTorrentName($filename);//截取应该显示的部分
		$encodeName = rawurlencode($filename);
		$ua = $_SERVER['HTTP_USER_AGENT'];
		header( 'Content-Description: File Transfer' );
		header('Content-Type: application/octet-stream');
// 		header("Content-Type: application/x-bittorrent");
		header("Accept-Ranges: bytes");
		if (preg_match('/MSIE|Trident/', $ua))//IE11的userAgent没有MSIE
		{
			header('Content-Disposition: attachment; filename="'.$encodeName.'"');
		}
		elseif (preg_match('/Firefox/', $ua))
		{
			header('Content-Disposition: attachment; filename="'.$filename.'"; charset=utf-8');
		}
		else 
		{
			header('Content-Disposition: attachment; filename="'.$filename.'"; charset=utf-8');
		}
		header ( 'Content-Transfer-Encoding: binary' );
		header ( 'Expires: 0' );
		header ( 'Cache-Control: must-revalidate' );
		header ( 'Pragma: public' );
// 		header('Content-Length: '.filesize($file));//不能要这个，添加了passkey文件已经变大
		flush();
		if (!empty($content))
		{
			echo $content;
		}
		else 
		{
			readfile($file);
		}
	}
	
	protected function getAjaxNavHtml($per, $total)
	{
		$HTML = '<ul class="pagination">';
		$HTML .= '<li class="disabled"><a class="prev"><span aria-hidden="true">&laquo;</span></a></li>';
		for ($i = 1; $i <= $total; $i++)
		{
			if ($i === 1)
			{
				$class = ' class="active"';
			}
			else
			{
				$class = '';
			}
			$HTML .= '<li'.$class.'><a><span aria-hidden="true">'.$i.'</span></a></li>';
		}
		if ($total == 1)
		{
			$class = ' class="disabled"';
		}
		else 
		{
			$class = '';
		}
		$HTML.= '<li'.$class.'><a class="next"><span aria-hidden="true">&raquo;</span></a></li>';
		$HTML .= '</ul>';
		return $HTML;
	}
	
	protected function getBreadcrumbs($delimiter = '<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>')
	{
		$breadcrumbs = '';
		if (!empty($this->breadcrumbs))
		{
			$count = count($this->breadcrumbs);
			foreach ($this->breadcrumbs as $k => $breadcrumb)
			{
				$breadcrumbs .= '<a';
				if (!empty($breadcrumb['url']))
				{
					$breadcrumbs .= ' href="'.$breadcrumb['url'].'"';
				}
				$breadcrumbs .= '>'.$breadcrumb['name'].'</a>';
				if ($k < $count-1)
				{
					$breadcrumbs .= $delimiter;
				}
			}
		}
		return $breadcrumbs;
	}
	
	protected function getMVCUrl()
	{
		if (MODULE == NULL)
		{
			$baseUrl = $this->createUrl(CONTROLLER.'/'.ACTION);
		}
		else
		{
			$baseUrl = $this->createUrl(MODULE.'/'.CONTROLLER.'/'.ACTION);
		}
		return $baseUrl;
	}
	
	/**
	 * 返回额外参数的urlencode结果
	 * @param array $retain 要保留不进行urlencode的参数名组成的数组
	 * @return string
	 */
	protected function getExtraParam(array $retain = array())
	{
		$urlQuery = $_SERVER['QUERY_STRING'];
		$out = '';
		if (!empty($urlQuery))
		{
			parse_str($urlQuery, $urlQueryArr);
			if (!empty($retain))
			{
				foreach ($retain as $key => $value)
				{
					unset($urlQueryArr[$value]);
				}
				if (!empty($urlQueryArr))
				{
					$string = http_build_query($urlQueryArr);
					$out = '&extra='.urlencode($string);
				}
						
			}
			else 
			{
				$out = '&extra='.urlencode($urlQuery);
			}
		}
		return $out;
	}
}