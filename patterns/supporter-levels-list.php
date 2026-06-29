<?php
/**
 * Title: Supporter Levels List
 * Slug: chance-ollie/supporter-levels-list
 * Description: Lists every support level (donation tier) as a heading, followed by the supporters assigned to that level. The nested Supporter Loop is filtered to the current level by theatrum_filter_query_loop_by_term().
 * Categories: query, posts
 * Keywords: supporter, donor, donation, level, term, query
 * Viewport Width: 800
 * Inserter: true
 */
?>
<!-- wp:terms-query {"termQuery":{"perPage":100,"taxonomy":"supporter-level","order":"asc","orderBy":"name","include":[],"hideEmpty":true,"showNested":false,"inherit":false}} -->
<div class="wp-block-terms-query">
	<!-- wp:term-template -->
		<!-- wp:term-name {"level":3} /-->

		<!-- wp:query {"namespace":"theatrum/supporter-loop","query":{"postType":"supporter","perPage":100,"pages":0,"offset":0,"order":"asc","orderBy":"title","author":"","search":"","exclude":[],"sticky":"","inherit":false}} -->
		<div class="wp-block-query">
			<!-- wp:post-template -->
				<!-- wp:post-title {"isLink":true} /-->
			<!-- /wp:post-template -->
		</div>
		<!-- /wp:query -->
	<!-- /wp:term-template -->
</div>
<!-- /wp:terms-query -->
