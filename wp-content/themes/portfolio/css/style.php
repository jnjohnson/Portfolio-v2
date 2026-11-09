<?php
// Compiles css/<p>.scss on request and caches the result in css/scss_cache/.
// Enqueued from ftheme::site_scripts() as style.php?p=style.scss&ver=v<mtime>.

use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\OutputStyle;

require __DIR__ . '/../vendor/autoload.php';

$directory = __DIR__;
$cache_dir = $directory . '/scss_cache';

// Only allow plain .scss filenames that live directly in this directory
$name = basename($_GET['p'] ?? '');
$input = $directory . '/' . $name;
if (substr($name, -5) !== '.scss' || !is_file($input)) {
	http_response_code(404);
	exit;
}

// Partials are @import'ed, so the newest .scss file decides whether the cache is stale
$mtime = max(array_map('filemtime', glob($directory . '/*.scss')));
$output = $cache_dir . '/' . basename($name, '.scss') . '-' . $mtime . '.css';

if (!is_file($output)) {
	try {
		$scss = new Compiler();
		$scss->setImportPaths($directory);
		$scss->setOutputStyle(OutputStyle::COMPRESSED);
		$css = $scss->compileString(file_get_contents($input), $input)->getCss();
	} catch (\Throwable $e) {
		http_response_code(500);
		header('Content-Type: text/css; charset=utf-8');
		echo '/* SCSS compile error: ' . str_replace('*/', '* /', $e->getMessage()) . ' */';
		exit;
	}

	if (!is_dir($cache_dir)) {
		mkdir($cache_dir, 0755, true);
	}
	// Remove stale builds of this file before writing the new one
	foreach (glob($cache_dir . '/' . basename($name, '.scss') . '-*.css') as $old) {
		unlink($old);
	}
	file_put_contents($output, $css, LOCK_EX);
}

$etag = '"' . md5($output) . '"';
header('Content-Type: text/css; charset=utf-8');
header('Cache-Control: public, max-age=31536000');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $mtime) . ' GMT');
header('ETag: ' . $etag);

if (($_SERVER['HTTP_IF_NONE_MATCH'] ?? '') === $etag) {
	http_response_code(304);
	exit;
}

readfile($output);
