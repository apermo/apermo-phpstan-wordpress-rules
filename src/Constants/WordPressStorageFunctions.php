<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Constants;

/**
 * Shared constant for WordPress storage functions and their value parameter indices.
 */
final class WordPressStorageFunctions {

	// phpcs:disable Apermo.DataStructures.ArrayComplexity.TooManyKeysError -- WordPress has many storage functions
	/**
	 * WordPress storage functions and the 0-based index of their value parameter.
	 *
	 * @var array<string, int>
	 */
	public const VALUE_PARAM_INDEX = [
		'add_option'             => 1,
		'update_option'          => 1,
		'add_network_option'     => 2,
		'update_network_option'  => 2,
		'add_metadata'           => 3,
		'update_metadata'        => 3,
		'update_metadata_by_mid' => 2,
		'add_post_meta'          => 2,
		'update_post_meta'       => 2,
		'add_user_meta'          => 2,
		'update_user_meta'       => 2,
		'add_comment_meta'       => 2,
		'update_comment_meta'    => 2,
		'add_term_meta'          => 2,
		'update_term_meta'       => 2,
		'add_site_meta'          => 2,
		'update_site_meta'       => 2,
		'set_transient'          => 1,
		'set_site_transient'     => 1,
	];
	// phpcs:enable Apermo.DataStructures.ArrayComplexity.TooManyKeysError
}
