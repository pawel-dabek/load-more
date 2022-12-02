<?php

function add_js_theme($name, $linkjs)
{
  return wp_enqueue_script($name, get_template_directory_uri() . $linkjs, array(), '1.0.0', true);
}

function et_load_more_button($attr)
{

  add_js_theme('et-alm', '/assets/js/et-ajax-load-more.js');

  $args = shortcode_atts(array(
    'offset' => 0,
    'per_load' => 3,
    'post_type' => get_post_type(),
    'search' => isset($GLOBALS["search_query"]) ? $GLOBALS["search_query"] : "",
    'taxonomy_name' => isset($GLOBALS["tax_name"]) ? $GLOBALS["tax_name"] : "",
    'taxonomy_field' => 'slug',
    'taxonomy_term' => isset($GLOBALS["term_name"]) ? $GLOBALS["term_name"] : "",
    'order' => 'DESC',
    'orderby' => 'date',
    'template_path' => $GLOBALS["post_template_src"],
    'label_text' => 'Load more',
    'loading_text' => 'loading...',
    'button_class' => 'btn-theme',
    'container_class' => 'wrapper-posts',
  ), $attr);

  $taxonomy = '';

  if ($args['taxonomy_name'] != '' && $args['taxonomy_field'] != '' && $args['taxonomy_term'] != '') {
    $taxonomy = array(
      'taxonomy' => $args['taxonomy_name'],
      'field' => $args['taxonomy_field'],
      'terms' => $args['taxonomy_term'],
    );
  }

  $query_args = array(
    'post_type' => $args['post_type'],
    'posts_per_page' => $args['per_load'],
    'offset' => $args['offset'],
    's' => $args['search'],
    'tax_query' => array($taxonomy),
  );

  $obj_name = new WP_Query($query_args);

  $output = '';
  $output .= '<div';
  $output .= ' data-allposts=' . $obj_name->found_posts . ' data-offset="' . $args['offset'] . '"';
  $output .= ' data-perload="' . $args['per_load'] . '" data-posttype="' . $args['post_type'] . '"';
  $output .= ' data-templatepath="' . $args['template_path'] . '" data-loadingtext="' . $args['loading_text'] . '"';
  $output .= ' data-labeltext="' . $args['label_text'] . '" data-order="' . $args['order'] . '"';
  $output .= ' data-search="' . $args['search'] . '" data-containerclass="' . $args['container_class'] . '"';
  $output .= ' data-orderby="' . $args['orderby'] . '" data-taxonomyname="' . $args['taxonomy_name'] . '"';
  $output .= ' data-taxonomyfield="' . $args['taxonomy_field'] . '" data-taxonomyterm="' . $args['taxonomy_term'] . '"';
  $output .= ' class="load-more-button ' . $args['button_class'] . '">' . $args['label_text'];
  $output .= '</div>';

  if (!file_exists(get_template_directory() . $args['template_path'])) {
    return "Nie podano ścieżki do post template bądź podany plik nie istnieje.";
  } elseif ($obj_name->found_posts > 0) {
    return $output;
  }
}

add_shortcode('et_load_more_button', 'et_load_more_button');

add_action('wp_ajax_et_load_more_posts', 'et_load_more_posts');
add_action('wp_ajax_nopriv_et_load_more_posts', 'et_load_more_posts');

function et_load_more_posts()
{

  if ($_POST['taxonomy_name'] != '' && $_POST['taxonomy_field'] != '' && $_POST['taxonomy_term'] != '') {
    $taxonomy = array(
      'taxonomy' => $_POST['taxonomy_name'],
      'field' => $_POST['taxonomy_field'],
      'terms' => $_POST['taxonomy_term'],
    );
  }

  $args = array(
    'post_type' => array($_POST['post_type']),
    'posts_per_page' => $_POST['per_load'],
    'offset' => $_POST['offset'],
    's' => $_POST['search'],
    'order' => $_POST['order'],
    'orderby' => $_POST['orderby'],
    'tax_query' => array($taxonomy),
  );

  $templatePath = preg_replace("/(.+)\.php$/", "$1", $_POST['template_path']);;

  query_posts($args);

  if (have_posts()) :

    while (have_posts()) : the_post();

      get_template_part($templatePath);

    endwhile;

  endif;

  die();
}
