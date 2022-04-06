Internal Flags
==============

[![Testing Suite](https://github.com/alleyinteractive/internal-flags/actions/workflows/unit-test.yml/badge.svg)](https://github.com/alleyinteractive/internal-flags/actions/workflows/unit-test.yml)

Creates a hidden taxonomy to improve expensive query performance by not relying on
meta queries. Allows for 'flags' to be set/unset on posts easily and entirely hidden
from the end-user.

## Instructions

By default the internal taxonomy will be added to all registered post types.

## Common Use Cases
- Hiding from archives/searches
- Posts by an author (where author is stored as a meta normally)
- Showing/hiding sponsored posts

## Usage

### Setting a flag on a post
```php
use Internal_Flags\set_flag;
set_flag( 'hide-from-archives', $post_id );
```

### Removing a flag on a post
```php
use Internal_Flags\remove_flag;
remove_flag( 'hide-from-archives', $post_id );
```

### Checking if a flag is on a post
```php
use Internal_Flags\has_flag;
has_flag( 'hide-from-archives', $post_id ); // bool
```

### Searching for posts with a flag
```php
use Internal_Flags\get_flag_tax_query;
$posts = new \WP_Query(
	[
		// ...
		'tax_query' => [
			get_flag_tax_query( 'show-on-page' )
		],
	],
);
```

### Searching for posts without a flag
_Inverse of the above_
```php
use Internal_Flags\get_flag_tax_query;
$posts = new \WP_Query(
	[
		// ...
		'tax_query' => [
			get_flag_tax_query( 'hide-from-archives', false )
		],
	],
);
```
