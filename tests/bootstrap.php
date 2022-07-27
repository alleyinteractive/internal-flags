<?php
/**
 * PHPUnit bootstrap file
 *
 * @package Internal_Flags
 */

use function Mantle\Testing\tests_add_filter;

require __DIR__ . '/../vendor/wordpress-autoload.php';

\Mantle\Testing\install(
	fn () => tests_add_filter(
		'muplugins_loaded',
		function() {
			require_once __DIR__ . '/../internal-flags.php';
		}
	),
);
