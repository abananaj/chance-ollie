<div class="jumbotron jumbotron-404">
  <div class="container">
    <h1>Page Not Found</h1>
    <h2><?php _e('Sorry, but this page has gone dark.', 'roots'); ?></h2>
    <p><?php _e('It looks like this was the result of either:', 'roots'); ?></p>
    <ul class="fa-ul">
      <li>
        <i class="fa-li fa fa-chevron-right"></i>
        <?php _e('a mistyped address', 'roots'); ?>
      </li>
      <li>
        <i class="fa-li fa fa-chevron-right"></i>
        <?php _e('an out-of-date link', 'roots'); ?>
      </li>
    </ul>
    <p>Let's try searching for your page instead&hellip;</p>
    <div class="row">
        <div class="col-sm-6">
            <?php get_search_form(); ?>
        </div>
    </div>

  </div>
</div>
