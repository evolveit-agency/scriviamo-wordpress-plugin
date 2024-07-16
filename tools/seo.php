<?php
/*
 * Register REST Api
 */
function scriviamo_seo_button_register_api_routes()
{
    register_rest_route('scriviamo-seo-button/v1', '/update-seo', array(
        'methods' => 'POST',
        'callback' => 'scriviamo_seo_button_update_seo',
        'permission_callback' => function () {
            return current_user_can('edit_posts');
        },
    ));
}
add_action('rest_api_init', 'scriviamo_seo_button_register_api_routes');

/*
 * Callback for Scriviamo API SEO
 */
function scriviamo_seo_button_update_seo(WP_REST_Request $request)
{
    $post_id = $request->get_param('post_id');
    $title = sanitize_text_field($request->get_param('title'));
    $description = sanitize_text_field($request->get_param('description'));

    if (!$post_id || !$title || !$description) {
        return new WP_Error('missing_parameters', 'Missing parameters', array('status' => 400));
    }

    update_post_meta($post_id, '_yoast_wpseo_title', $title);
    update_post_meta($post_id, '_yoast_wpseo_metadesc', $description);

    return new WP_REST_Response('SEO fields updated', 200);
}

