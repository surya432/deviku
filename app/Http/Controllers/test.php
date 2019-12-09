<?php
/*
    Plugin Name: custom post types sample
    Plugin URI: http://tagdiv.com
    Description: adds the td_book post type and td_author + td_genre taxonomy
    Author: Surya Haho
    Version: 2.0
    Author URI: http://nontonindrama.com
*/

// to register Custom Post Types and taxonomies, the use of the init hook is required!
add_action('init', 'td_custom_post_type_init', 0);
function td_custom_post_type_init()
{


    /**
     * Add new taxonomy, make it hierarchical (like categories) and associate it to the td_books Custom Post Type
     * https://codex.wordpress.org/Function_Reference/register_taxonomy
     */
    $labels = array(
        'name'              => _x('Genres', 'taxonomy general name'),
        'singular_name'     => _x('Genre', 'taxonomy singular name'),
        'search_items'      => __('Search Genres'),
        'all_items'         => __('All Genres'),
        'parent_item'       => __('Parent Genre'),
        'parent_item_colon' => __('Parent Genre:'),
        'edit_item'         => __('Edit Genre'),
        'update_item'       => __('Update Genre'),
        'add_new_item'      => __('Add New Genre'),
        'new_item_name'     => __('New Genre Name'),
        'menu_name'         => __('Genre'),
    );
    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'genre'),
        'show_in_rest'      => true,
        'rest_base'         => 'genre-api',
        'rest_controller_class' => 'WP_REST_Terms_Controller',
    );
    register_taxonomy('genre', array('page', 'post'), $args);

    /**
     * Add new taxonomy, make it hierarchical (like categories) and associate it to the td_books Custom Post Type
     * https://codex.wordpress.org/Function_Reference/register_taxonomy
     */
    $labels = array(
        'name'              => _x('Status', 'taxonomy general name'),
        'singular_name'     => _x('Status', 'taxonomy singular name'),
        'search_items'      => __('Search Status'),
        'all_items'         => __('All Status'),
        'parent_item'       => __('Parent Status'),
        'parent_item_colon' => __('Parent Status:'),
        'edit_item'         => __('Edit Status'),
        'update_item'       => __('Update Status'),
        'add_new_item'      => __('Add New Status'),
        'new_item_name'     => __('New Status Name'),
        'menu_name'         => __('Status'),
    );
    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'status'),
        'show_in_rest'       => true,
        'rest_controller_class' => 'WP_REST_Posts_Controller',
        'show_in_rest'       => true,
        'rest_base'          => 'status-api',
        'rest_controller_class' => 'WP_REST_Terms_Controller',

    );
    register_taxonomy('status', array('page', 'post'), $args);

    /**
     * Add new taxonomy, make it hierarchical (like categories) and associate it to the td_books Custom Post Type
     * https://codex.wordpress.org/Function_Reference/register_taxonomy
     */
    $labels = array(
        'name'              => _x('Country', 'taxonomy general name'),
        'singular_name'     => _x('country', 'taxonomy singular name'),
        'search_items'      => __('Search country'),
        'all_items'         => __('All country'),
        'parent_item'       => __('Parent country'),
        'parent_item_colon' => __('Parent country:'),
        'edit_item'         => __('Edit country'),
        'update_item'       => __('Update country'),
        'add_new_item'      => __('Add New country'),
        'new_item_name'     => __('New country Name'),
        'menu_name'         => __('Country'),
    );
    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'country'),
        'show_in_rest'      => true,
        'rest_base'         => 'country-api',
        'rest_controller_class' => 'WP_REST_Posts_Controller',

    );
    register_taxonomy('country', array('page', 'post'), $args);

    /**
     * Add new taxonomy, NOT hierarchical (like tags)  and associate it to the td_books Custom Post Type
     * https://codex.wordpress.org/Function_Reference/register_taxonomy
     */
    $labels = array(
        'name'                       => _x('Actor', 'taxonomy general name'),
        'singular_name'              => _x('Actor', 'taxonomy singular name'),
        'search_items'               => __('Search Actor'),
        'popular_items'              => __('Popular Actor'),
        'all_items'                  => __('All Actor'),
        'parent_item'                => null,
        'parent_item_colon'          => null,
        'edit_item'                  => __('Edit Actor'),
        'update_item'                => __('Update Actor'),
        'add_new_item'               => __('Add New Actor'),
        'new_item_name'              => __('New Actor Name'),
        'separate_items_with_commas' => __('Separate Actor with commas'),
        'add_or_remove_items'        => __('Add or remove Actor'),
        'choose_from_most_used'      => __('Choose from the most used Actor'),
        'not_found'                  => __('No writers found.'),
        'menu_name'                  => __('Actor'),
    );
    $args = array(
        'hierarchical'          => false,
        'labels'                => $labels,
        'show_ui'               => true,
        'show_admin_column'     => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var'             => true,
        'rewrite'               => array('slug' => 'actor'),
        'show_in_rest'          => true,
        'rest_base'             => 'actor-api',
        'rest_controller_class' => 'WP_REST_Terms_Controller',

    );
    register_taxonomy('actor', array('page', 'post'), $args);
}


/**
 * this hook will regenerate the permalinks when the plugin is activated. I would recommend that you work with the permalinks OFF until you make
 * your custom post types. After you make the final custom post types you can enable the permalinks and hit save in wp-admin -> settings -> permalinks.
 */
function td_regenerate_htaccess()
{
    td_custom_post_type_init();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'td_regenerate_htaccess');

class all_terms
{
    public function __construct()
    {
        $version = '2';
        $namespace = 'wp/v' . $version;
        $base = 'search-terms';
        register_rest_route($namespace, '/' . $base, array(
            'methods' => 'GET',
            'callback' => array($this, 'get_all_terms'),
        ));
    }
    public function get_all_terms($object)
    {
        $return = [];
        $taxonomy_name = $_POST['term'];
        $keyword = explode(",", $_POST['s']);
        foreach ($keyword as $s) {
            $term = get_term_by('name', $s, $taxonomy_name);
            if ($term->term_id) {
                $return[] = strval($term->term_id);
            } else {
                wp_insert_term($s, $taxonomy_name);
                $term = get_term_by('name', $s, $taxonomy_name);
                if ($term->term_id) {
                    $return[] = strval($term->term_id);
                }
            }
        }
        return new WP_REST_Response($return, 200);
    }
}
class all_meta
{
    public function __construct()
    {
        $version = '2';
        $namespace = 'wp/v' . $version;
        $base = 'all-meta';
        register_rest_route($namespace, '/' . $base, array(
            'methods' => 'GET',
            'callback' => array($this, 'get_all_meta'),
        ));
    }
    public function get_all_meta($object)
    {
        $return = [];
        $postId = $_GET['id'];
        $return = get_post_meta($postId);
        return new WP_REST_Response($return, 200);
    }
}
add_action('rest_api_init', function () {
    $all_terms = new all_terms;
    $all_meta = new all_meta;
});
