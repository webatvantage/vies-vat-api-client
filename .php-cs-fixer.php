<?php

declare(strict_types=1);

use Webatvantage\PhpCsFixer\Config\Config;

$finder = PhpCsFixer\Finder::create()
	->in([
		__DIR__ . '/src',
		__DIR__ . '/tests',
	])
	->name('*.php')
	->ignoreDotFiles(true)
	->ignoreVCS(true);

$config = Config::default()
	->setFinder($finder);

// This library supports PHP 7.4+. A trailing comma in a parameter list is a
// PHP 8.0 syntax and fatals on 7.4, so it must not be enforced. Keep trailing
// commas everywhere else (arguments, arrays, ... are valid since PHP 7.3).
$rules = $config->getRules();
$rules['trailing_comma_in_multiline'] = [
	'elements' => ['arguments', 'array_destructuring', 'arrays', 'match'],
];

return $config->setRules($rules);