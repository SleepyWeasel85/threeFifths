<?php
namespace ThreeFifths\Blog;

use DirectoryIterator;
use Gt\WebEngine\FileSystem\Path;
use Psr\Http\Message\UriInterface;

class BlogRepository {
	/**
	 * @return Article[]
	 */
	public static function getLatest(int $num = 10):array {
		$articleList = [];

		$fileList = self::getFileList();
		$fileList = array_reverse($fileList);

		foreach($fileList as $i => $filePath) {
			if($i >= $num - 1) {
				break;
			}

			$articleList []= new Article($filePath);
		}

		return $articleList;
	}

	protected static function getFileList():array {
		$fileList = [];
		$directoryPath = self::getDirectoryPath();

		foreach(new DirectoryIterator($directoryPath) as $fileInfo) {
			if($fileInfo->isDot()
			|| $fileInfo->isDir()) {
				continue;
			}

			$fileList []= $fileInfo->getPathname();
		}

		sort($fileList);
		return $fileList;
	}

	protected static function getDirectoryPath():string {
		return implode(DIRECTORY_SEPARATOR, [
			Path::getDataDirectory(),
			"blog",
		]);
	}

	public static function getByDateAndTitle(
		int $year,
		int $month,
		int $day,
		string $title
	):Article {
		$directoryPath = self::getDirectoryPath();

		$fileName = implode("-", [
			$year,
			str_pad($month, 2, "0", STR_PAD_LEFT),
			str_pad($day, 2, "0", STR_PAD_LEFT),
			"$title.md",
		]);

		$pathName = implode(DIRECTORY_SEPARATOR, [
			$directoryPath,
			$fileName,
		]);

		if(!is_file($pathName)) {
			throw new ArticleNotFoundException($pathName);
		}

		return new Article($pathName);
	}
}