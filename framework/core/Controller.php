<?php
namespace framework\core;

use framework\App;
use framework\core\Log;
use framework\core\Router;

class Controller
{
	protected $layout;
	protected $pageTitle;

	
	public function __construct()
	{
		if (defined('FLUSH_CACHE'))
		{
			App::ins()->mem->flush();//清除缓存		
		}
		$this->init();
	}
	
	protected function init()
	{
		/* 控制器初始化会执行 */
	}
	
	protected function setPageTitle($title)
	{
		$this->pageTitle = $title;
	}
	
	protected function getPageTitle()
	{
		if(empty($this->pageTitle))
		{
			$m = '';
			if(MODULE !== NULL)
			{
				$m = MODULE.'-';
			}
			return $m.CONTROLLER.'-'.ACTION;
		}
		else 
		{
			return $this->pageTitle;
		}
	
	}
	
	protected function redirect($mvc, $options = array())
	{
		if (defined('STOP_REDIRECT') && (STOP_REDIRECT === 1 || STOP_REDIRECT === TRUE))
		{
			exit('defined stop redirect !');
		}
		$url = $this->createUrl($mvc, $options);
		header("Location:".$url);exit;
	}
	
	protected function createUrl($mvc, $options = array())
	{
		return Router::createUrl($mvc, $options);
	}
	
	protected function getViewPath()
	{
		if(MODULE === NULL)
		{
			return APP_PATH.'protect'.DS.'views'.DS;
		}
		else
		{
			return APP_PATH.'protect'.DS.'modules'.DS.strtolower(MODULE).DS.'views'.DS;
		}
	}
	
	protected function getViewFile($path)
	{
		if(file_exists($path))
		{
			return $path;
		}
		elseif(file_exists($file = $this->getViewPath().$path.'.php'))
		{
			return $file;
		}
		elseif(file_exists($file = $this->getViewPath().strtolower(CONTROLLER).DS.$path.'.php'))
		{
			return $file;
		}
		else 
		{
			trigger_error('找不到视图文件：'.$path, E_USER_ERROR);exit;
		}
	}
	
	protected function getLayoutFile($path)
	{
		if(file_exists($path))
		{
			return $path;
		}
		elseif(file_exists($file = $this->getViewPath().'layouts'.DS.$path.'.php'))
		{
			return $file;
		}
		else
		{
			trigger_error('找不到布局文件：'.$path, E_USER_ERROR);exit;
		}
	}
	
	protected function render($view, $data = array())
	{
		$view = $this->getViewFile($view);
		$content = $this->renderFile($view, $data, FALSE);
		
		if(!empty($this->layout))
		{
			$layout = $this->getLayoutFile($this->layout);
			ob_start();
			ob_implicit_flush(0);//关闭绝对刷送
			require $layout;
			if(App::isDebug())
			{
				Log::requireFile($layout);
			}
			$content = ob_get_clean();
		}
		if(Cache::$_doPageCache)
		{
			Cache::setPageCache($content);
		}
		return $content;
		
	}
	
	protected function renderPartial($view, $data = array())
	{
		$view = $this->getViewFile($view);
		$content = $this->renderFile($view, $data, TRUE);
		if(Cache::$_doPageCache)
		{
			Cache::setPageCache($content);
		}
		return $content;
	}
	
	private function renderFile($file, $data, $close = FALSE)
	{
		extract($data);//覆盖已存在的变量
		ob_start();
		ob_implicit_flush(0);//关闭绝对刷送
		require $file;
		if(App::isDebug())
		{
			Log::requireFile($file);
		}
 		if($close)
		{
 			return ob_get_clean();//得到内容，清空并关闭缓冲区
		}
		else
 		{
			$result = ob_get_contents();
 			ob_end_clean();//清空缓冲区
 			return $result;
 		}
	}
	
	protected function getRunInfo($onlyArr = FALSE)
	{
		$out = array(
				'year' => '2015~'.date('Y'),
				'PowerBy' => 'TinyHD',
				'queries' => App::ins()->db->getQueries(),
				'time' => number_format(microtime(TRUE) - App::getStartTime(), 4),
				'MemcacheOn' => App::isComponentEnabled('Memcache'),
		);
		if (!$onlyArr)
		{
			$str = '<div class="container" style="margin-top: 50px;margin-bottom: 50px"><div class="row"><div class="col-xs-12"><footer class="text-center">';
			$str .= '&copy; '.$out['year'].'  ';
			$str .= 'Powered By <a href="/about">'.$out['PowerBy'].'</a></br>';
			$str .= 'Page created in '.$out['time'].' seconds with '.$out['queries'].' queries';
			$str .= $out['MemcacheOn'] ? ',Memcache On' : ' Memcache Off';
			$str .= '</footer></div></div></div>';
			return $str;
		}
		else
		{
			return $out;
		}
	}
	
}