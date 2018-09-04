<?php
namespace ThreeFifths\Page;

use Gt\WebEngine\Logic\Page;
use ThreeFifths\Blog\BlogRepository;

class IndexPage extends Page {
	public function go() {
		$latestArticles = BlogRepository::getLatest();
		$main = $this->document->querySelector("main");
		$main->bind($latestArticles);
	}
}