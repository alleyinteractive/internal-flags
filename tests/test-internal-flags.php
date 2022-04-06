<?php
/**
 * Test_Internal_Flags class file
 *
 * @package Internal_Flags
 */

use Mantle\Testing\Framework_Test_Case;

use function Internal_Flags\{
	get_flag_tax_query,
	has_flag,
	remove_flag,
	set_flag,
};

/**
 * Test Internal Flags
 */
class Test_Internal_Flags extends Framework_Test_Case {

	/**
	 * Test setting a flag.
	 */
	public function test_setting_flag() {
		$post_id = $this->factory->post->create();
		$this->assertFalse( has_flag( 'flag-to-check', $post_id ) );

		set_flag( 'flag-to-check', $post_id );
		$this->assertTrue( has_flag( 'flag-to-check', $post_id ) );

		remove_flag( 'flag-to-check', $post_id );
		$this->assertFalse( has_flag( 'flag-to-check', $post_id ) );
	}

	/**
	 * Test querying posts with a flag.
	 */
	public function test_querying_flags() {
		$post_id = $this->factory->post->create();
		set_flag( 'query-flag', $post_id );

		$post_ids = \get_posts(
			[
				'fields'           => 'ids',
				'post_type'        => 'post',
				'suppress_filters' => false,
				'tax_query'        => [
					get_flag_tax_query( 'query-flag', true ),
				],
			]
		);

		$this->assertContains( $post_id, $post_ids );

		// Ensure it doesn't include the post if excluded.
		$post_ids = \get_posts(
			[
				'fields'           => 'ids',
				'post_type'        => 'post',
				'suppress_filters' => false,
				'tax_query'        => [
					get_flag_tax_query( 'query-flag', false ),
				],
			]
		);

		$this->assertNotContains( $post_id, $post_ids );

		// Remove it and make sure it doesn't contain it now.
		remove_flag( 'query-flag', $post_id );

		$post_ids = \get_posts(
			[
				'fields'           => 'ids',
				'post_type'        => 'post',
				'suppress_filters' => false,
				'tax_query'        => [
					get_flag_tax_query( 'query-flag', true ),
				],
			]
		);

		$this->assertNotContains( $post_id, $post_ids );
	}
}
