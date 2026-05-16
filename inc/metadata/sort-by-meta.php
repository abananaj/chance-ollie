<?php 

// This function will allow the user to select a meta key to sort posts by in the admin area. It will add a dropdown to the "Sort by" options in the post list table, and will modify the query to sort by the selected meta key

if ( ! function_exists( 'chance_custom_sort_by_meta' ) ) {
	/**
	 * Enable sorting by meta key in the WordPress admin post list.
	 *
	 * @param string $post_type The post type to enable sorting for. Default is 'post'.
	 * @param array  $meta_keys Array of meta keys to allow sorting by.
	 * @return void
	 */
	function chance_custom_sort_by_meta( $post_type = 'post', $meta_keys = [] ) {
		if ( empty( $meta_keys ) ) {
			return;
		}

		// Add sortable columns for each meta key
		add_filter( "manage_{$post_type}_posts_columns", function( $columns ) use ( $meta_keys ) {
			foreach ( $meta_keys as $key => $label ) {
				$columns[ "meta_$key" ] = $label;
			}
			return $columns;
		} );

		// Register the columns as sortable
		add_filter( "manage_edit-{$post_type}_sortable_columns", function( $columns ) use ( $meta_keys ) {
			foreach ( $meta_keys as $key => $label ) {
				$columns[ "meta_$key" ] = $key;
			}
			return $columns;
		} );

		// Handle the sorting query modification
		add_filter( 'posts_clauses', function( $clauses ) use ( $post_type, $meta_keys ) {
			global $wpdb, $wp_query;

			// Only apply to admin queries for the correct post type
			if ( ! is_admin() || ! isset( $wp_query->query['orderby'] ) ) {
				return $clauses;
			}

			$orderby = $wp_query->query['orderby'];

			// Check if this is a meta key sort
			$meta_key = str_replace( 'meta_', '', $orderby );

			if ( in_array( $meta_key, array_keys( $meta_keys ), true ) && strpos( $orderby, 'meta_' ) === 0 ) {
				// Get the order direction
				$order = isset( $wp_query->query['order'] ) ? $wp_query->query['order'] : 'ASC';

				// Join the postmeta table if not already joined
				if ( false === strpos( $clauses, $wpdb->postmeta ) ) {
					$clauses .= " LEFT JOIN {$wpdb->postmeta} ON ({$wpdb->posts}.ID = {$wpdb->postmeta}.post_id)";
				}

				// Add the ORDER BY clause for the meta key
				$clauses = preg_replace( '/ORDER BY.*/', '', $clauses );
				$clauses .= $wpdb->prepare(
					" AND {$wpdb->postmeta}.meta_key = %s ORDER BY {$wpdb->postmeta}.meta_value {$order}",
					$meta_key
				);
			}

			return $clauses;
		} );
	}
}
