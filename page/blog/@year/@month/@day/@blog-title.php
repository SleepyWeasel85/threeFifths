<?php
namespace ThreeFifths\Page\Blog\_Year\_Month\_Day;

use Gt\WebEngine\Logic\Page;
use ThreeFifths\Blog\ArticleNotFoundException;
use ThreeFifths\Blog\BlogRepository;

class _BlogTitlePage extends Page {
	public function go() {
		try {
			$article = BlogRepository::getByDateAndTitle(
				$this->dynamicPath->get("year"),
				$this->dynamicPath->get("month"),
				$this->dynamicPath->get("day"),
				$this->dynamicPath->get("blog-title")
			);
		}
		catch(ArticleNotFoundException $exception) {
			http_response_code(404);
			exit;
		}

		$main = $this->document->querySelector("main");
		$main->bind($article);
	}
}