<?php
/**
 * Created by PhpStorm.
 * User: rios0rios0
 * Date: 20/03/19
 * Time: 19:29
 */

class ViewsManagement
{
	protected $root;
	protected $vars = array();
	protected $header = "/app/views/fragments/header.php";
	protected $footer = "/app/views/fragments/footer.php";
	protected $content = "";
	protected $layout = "/app/views/layouts/default.php";

	public function __construct()
	{
		$this->root = $_SERVER["DOCUMENT_ROOT"] . RoutesManagement::$app;
		$this->header = $this->root . $this->header;
		$this->footer = $this->root . $this->footer;
		$this->content = $this->root . $this->content;
		$this->layout = $this->root . $this->layout;
	}

	public function set($fragment, $path)
	{
		$abs = $this->root . $path;
		$real = realpath($abs);
		if ($real === false || strpos($real, $this->root) !== 0) {
			throw new Exception("Invalid path: " . $path);
		}
		$this->$fragment = $real;
	}

	public function render()
	{
		include $this->layout;
	}

	public function __get($name)
	{
		return $this->vars[$name];
	}

	public function __set($name, $value)
	{
		$this->vars[$name] = $value;
	}
}