<?php
class CommonController extends Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->checkLogin();
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
	
	protected function getTorrentTTL($time, $delimiter = '<br/>')
	{
		if(empty($time))
		{
			return NULL;
		}
		if(!ctype_digit($time))
		{
			trigger_error('getTorrentTTL()只接收时间戳', E_USER_NOTICE);
		}
		$d = time() - intval($time);//过去了这么多秒
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
	
	protected function getTorrentSize($size)
	{
		if(empty($size))
		{
			return NULL;
		}
		if(!ctype_digit($size))
		{
			trigger_error('getTorrentSize()只接收正整数', E_USER_NOTICE);
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
	/**
	 * 返回分页的HTML代码
	 * Enter description here ...
	 * @param unknown_type $page
	 * @param unknown_type $per
	 * @param unknown_type $total
	 */
	protected function getNavHtml($page = 1, $per = 10, $total)
	{
		$url = self::getNavHref();

		$HTML = '<ul class="pagination">';
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
		$baseUrl = $this->createUrl(CONTROLLER.'/'.ACTION);
		if(!empty($queryStr))
		{
			return $baseUrl.'?'.$queryStr.'&';
		}
		else
		{
			return $baseUrl.'?';
		}
	}
	
	public function getSortHref($field, $text)
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
					$direction = 'down';
				}
				else 
				{
					$urlQueryArr['sort_type'] = 'asc';
					$direction = 'up';
				}
			}
			else
			{
				$urlQueryArr['sort_field'] = $field;
				$urlQueryArr['sort_type'] = 'desc';
				$direction = 'down';
			}
			unset($urlQueryArr['page']);
			$queryStr = http_build_query($urlQueryArr);
		}
		else
		{
			$queryStr = 'sort_field='.$field.'&sort_type=desc';
			$direction = 'down';
		}
		$baseUrl = $this->createUrl(CONTROLLER.'/'.ACTION);
		$href = $baseUrl.'?'.$queryStr;
		$icon = '';
		if (isset($_GET['sort_field']) && $field === $_GET['sort_field'])
		{
			$icon = "<span class='glyphicon glyphicon-arrow-$direction' aria-hidden='true'></span>";
		}
		return "<a href='$href'>{$text}{$icon}</a>";
	}
}