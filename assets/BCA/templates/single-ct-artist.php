<?php
  use BCA\ChanceTheater\Helpers;

  while (have_posts()) : the_post();
?>

<div class="entry-content">
  <h2>Artist Profile</h2>
  <?php the_content(); ?>
</div>

<h2>Productions <small>at Chance Theater</small></h2>
<table class="table table-responsive">
  <thead>
    <tr>
      <th>Production</th>
      <th>Date</th>
      <th>Role</th>
    </tr>
  </thead>
  <tbody>
  <?php
    $wp_query_roles = new WP_Query(array(
      'meta_key' => 'artist',
      'meta_value' => get_the_ID(),
      'post_type' => 'ct-production-role',
      'posts_per_page' => 200,
      'orderby' => 'date'
    ));

    $roles = array();
    $roles_dates = array();
    foreach ($wp_query_roles->posts as $id=>$row) {
      $a_role = array();
      $time = get_post_meta($row->production, 'date-opening', true);
      $a_role['production_title'] = get_the_title($row->production);
      $a_role['production_permalink'] = get_permalink($row->production);
      $a_role['opening_date'] = $time;
      $a_role['role'] = $row->role;
      $roles[$time + $id] = $a_role;
    }
    krsort($roles);

    foreach ($roles as $role) :
  ?>
    <tr>
      <td>
        <a href="<?php echo $role['production_permalink'] ?>">
          <?php echo $role['production_title'] ?>
        </a>
      </td>
      <td><?php echo date('F Y', $role['opening_date']) ?></td>
      <td><?php echo $role['role'] ?></td>
    </tr>
  <?php $year = date('Y', $role['opening_date']); ?>
  <?php endforeach; ?>
  </tbody>
</table>

<?php endwhile; ?>
