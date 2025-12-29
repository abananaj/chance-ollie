<?php if (of_get_option('subscriptions_btn_enabled')) : ?>
<?php $permalink_s = home_url(get_page_uri(of_get_option('subscriptions_btn_link_id'))) ?>
<?php $permalink_m = home_url(get_page_uri(of_get_option('memberships_btn_link_id'))) ?>
<div class="clearfix"></div>
<aside class="panel panel-default subscribe-cta">
  <div class="panel-body">
    <h2>
      <a href="<?php echo $permalink_s ?>" class="text-info">
      <?php echo of_get_option('subscriptions_cta_headline') ?>
      </a>
    </h2>
    <div class="lead">
      <?php echo of_get_option('subscriptions_cta_body') ?>
    </div>
    <a href="<?php echo $permalink_s ?>" class="btn btn-primary">
      <?php echo of_get_option('subscriptions_cta_link_text') ?>
    </a>&nbsp;
    <a href="<?php echo $permalink_m ?>" class="btn btn-secondary">
      <?php echo of_get_option('memberships_cta_link_text') ?>
    </a>
  </div>
</aside>
<?php endif; ?>
