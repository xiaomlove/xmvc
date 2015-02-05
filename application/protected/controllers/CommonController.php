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
	
	protected function getNavHtml($page = 1, $per = 10, $total)
	{
		if(isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING']))
		{
			$queryStr = $_SERVER['QUERY_STRING'].'&';
		}
		else
		{
			$queryStr = '?';
		}
		$url = $this->createUrl(CONTROLLER.'/'.ACTION);
		$HTML = '<ul class="pagination">';
		if ($total <= 10)//总页数少于10页
		{
			if($page === 1)
			{
				$HTML .= '<li class="disabled"><a><span aria-hidden="true">&laquo;</span></a></li>';
			}
			else 
			{
				$HTML .= '<li><a href="'.$url.$queryStr."page=".($page-1).'"><span aria-hidden="true">&laquo;</span></a></li>';
			}
			for ($i = 1; $i <= $total; $i++)
			{
				$class = $i === $page ? ' class="active"' : '';
				$HTML .= '<li'.$class.'><a href="'.$url.$queryStr.'page='.$i.'"><span aria-hidden="true">'.$i.'</span></a></li>';
			}
			if($page === $total)
			{
				$HTML .= '<li class="disabled"><a><span aria-hidden="true">&raquo;</span></a></li>';
			}
			else 
			{
				$HTML .= '<li><a href="'.$url.$queryStr."page=".($page+1).'"><span aria-hidden="true">&raquo;</span></a></li>';
			}
		}
		else
		{
			
		}
		$HTML .= '</ul>';
		return $HTML;
		
	}
}