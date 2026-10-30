<?php
/**
 * PHPUnit bootstrap file
 *
 * @package Internal_Flags
 */

\Mantle\Testing\manager()
	->loaded( fn () => require_once dirname( __DIR__ ) . '/internal-flags.php' )
	->install();
