<?php
/**
 * Plugin Name:     Internal Flags
 * Plugin URI:      https://alley.co/
 * Description:     Use a hidden taxonomy to improve expensive queries.
 * Author:          Alley Interactive
 * Author URI:      https://alley.co/
 * Text Domain:     internal-flags
 * Version:         0.1.0
 *
 * @package         Internal_Flags
 */

namespace Internal_Flags;

/**
 * Taxonomy name.
 *
 * @var string
 */
const TAXONOMY = 'internal-flags';

/**
 * Register the internal flags taxonomy.
 */
function register_taxonomy() {
	\register_taxonomy(
		TAXONOMY,
		\array_keys( \get_post_types() ),
		[
			'public'       => false,
			'rewrite'      => false,
			'show_in_rest' => false,
		]
	);
}
\add_filter( 'init', __NAMESPACE__ . '\register_taxonomy' );

/**
 * Set an internal flag for a post.
 *
 * @param string $flag Flag to set.
 * @param int    $post_id Post ID.
 * @return bool
 */
function set_flag( string $flag, int $post_id ): bool {
	$term_id = get_flag_term_id( $flag );
	if ( ! $term_id ) {
		return false;
	}

	// Bail if the flag is already set.
	if ( \has_term( $term_id, TAXONOMY, $post_id ) ) {
		return true;
	}

	\wp_set_object_terms( $post_id, $term_id, TAXONOMY, true );
	return true;
}

/**
 * Remove an internal flag for a post.
 *
 * @param string $flag Flag to set.
 * @param int    $post_id Post ID.
 */
function remove_flag( string $flag, int $post_id ) {
	$term_id = get_flag_term_id( $flag );
	if ( ! $term_id ) {
		return;
	}

	if ( \has_term( $term_id, TAXONOMY, $post_id ) ) {
		\wp_remove_object_terms( $post_id, $term_id, TAXONOMY );
	}
}

/**
 * Check if a post has an internal flag set.
 *
 * @param string $flag Flag to check.
 * @param int    $post_id Post ID.
 * @return bool
 */
function has_flag( string $flag, int $post_id ): bool {
	return \has_term( $flag, TAXONOMY, $post_id );
}

/**
 * Retrieve a taxonomy query for posts with a flag.
 *
 * Note: will not create the flag term if the flag doesn't already exist.
 * Flags are only created through `set_flag()`/`remove_flag()`.
 *
 * @param string $flag Flag name.
 * @param bool   $includes Include or does not include posts with the flag.
 * @return array
 */
function get_flag_tax_query( string $flag, bool $includes = true ): array {
	$term_id = get_flag_term_id( $flag );
	if ( ! $term_id ) {
		return [];
	}

	return [
		'field'            => 'term_id',
		'include_children' => false,
		'operator'         => $includes ? 'IN' : 'NOT IN',
		'taxonomy'         => TAXONOMY,
		'terms'            => $term_id,
	];
}

/**
 * Get the term ID for a flag.
 *
 * @param string $flag Flag name.
 * @return int|null
 */
function get_flag_term_id( string $flag ): ?int {
	$term = \get_term_by( 'slug', $flag, TAXONOMY );

	if ( ! ( $term instanceof \WP_Term ) ) {
		$term = \wp_insert_term(
			$flag,
			TAXONOMY,
			[
				'slug' => $flag,
			]
		);

		if ( \is_wp_error( $term ) ) {
			return null;
		}

		if ( empty( $term['term_id'] ) ) {
			return null;
		}

		return (int) $term['term_id'];
	}


	return (int) $term->term_id;
}
