<?php
class Controller
{
	protected $layout;
	protected $pageTitle;

	
	public function __construct()
	{
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
		$url = $this->createUrl($mvc, $options);
		header("Location:".$url);
	}
	
	protected function createUrl($mvc, $options = array())
	{
		return Router::createUrl($mvc, $options);
	}
	
	protected function getViewPath()
	{
		if(MODULE === NULL)
		{
			return APP_PATH.'protected'.DS.'views'.DS;
		}
		else
		{
			return APP_PATH.'protected'.DS.'modules'.DS.strtolower(MODULE).DS.'views'.DS;
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
	
	
	
	
}