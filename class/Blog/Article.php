<?php
namespace ThreeFifths\Blog;

use DateTime;
use Gt\Dom\HTMLDocument;
use Gt\DomTemplate\BindableData;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extras\SmartPunct\SmartPunctExtension;

class Article {
	protected $filePath;
	protected $markdown;
	protected $document;

	public $title;
	public $subtitle;
	public $preview;
	public $content;
	public $date;

	public $dateString;
	public $year;
	public $month;
	public $day;

	public function __construct(string $filePath) {
		$this->filePath = $filePath;
		$this->markdown = file_get_contents($this->filePath);
		$this->document = new HTMLDocument(
			$this->parse()
		);

		$this->title = $this->loadTitle();
		$this->subtitle = $this->loadSubtitle();
		$this->titleStub = strtolower(
			str_replace(
				" ",
				"-",
				$this->title
			)
		);
		$this->preview = $this->loadContentPreview();
		$this->content = $this->loadContent();
		$this->date = $this->loadDate();
		$this->dateString = $this->date->format("jS F Y");
		$this->year = $this->date->format("Y");
		$this->month = $this->date->format("m");
		$this->day = $this->date->format("d");
	}

	protected function parse($markdown = null):string {
		if(is_null($markdown)) {
			$markdown = $this->markdown;
		}

		$config = [];
		$environment = Environment::createCommonMarkEnvironment();
		$environment->addExtension(
			new SmartPunctExtension()
		);
		$converter = new CommonMarkConverter(
			$config,
			$environment
		);

		return $converter->convertToHtml(
			$markdown
		);
	}

	protected function loadTitle():string {
		return $this->document->querySelector("h1")->innerText;
	}

	protected function loadSubtitle():string {
		return $this->document->querySelector("h2")->innerText;
	}

	protected function loadContent():string {
		$body = $this->document->body->cloneNode(true);
		$body->querySelector("h1")->remove();
		$body->querySelector("h2")->remove();
		return $body->innerHTML;
	}

	protected function loadContentPreview($length = 500):string {
		$markdown = strip_tags($this->markdown);
		preg_match_all(
			"/^\[.+\]: .+$/m",
			$markdown,
			$linkMatches
		);

// Strip off the h1 and h2:
		while($markdown[0] === "#") {
			$markdown = substr($markdown,
				strpos(
					$markdown,
					PHP_EOL
				) + 1
			);
		}

		$markdown = substr(
			$markdown,
			0,
			strpos(
				$markdown,
				PHP_EOL,
				$length
			)
		);

		$markdown .= PHP_EOL;

		foreach($linkMatches[0] as $link) {
			$markdown .= PHP_EOL . $link;
		}

		return $this->parse($markdown);
	}

	protected function loadDate():DateTime {
		$filename = pathinfo(
			$this->filePath,
			PATHINFO_FILENAME
		);
		$dateFormat = "YYYY-MM-DD";

		$dateString = substr(
			$filename,
			0,
			strlen($dateFormat)
		);

		return new DateTime($dateString);
	}
}