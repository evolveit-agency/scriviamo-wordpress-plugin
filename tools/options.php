<?php
/*
 * Scriviamo Options page
 */
function scriviamo_seo_settings_init() {
    add_settings_section(
        'scriviamo_seo_section',
        'Scriviamo Settings',
        null,
        'scriviamo-seo'
    );


    $classArr = [];
    if(get_option("scriviamo_seo_token", "") != ''){
        $classArr['class'] = "hidden";
    }

    add_settings_field(
        'scriviamo_seo_email',
        'Email',
        'scriviamo_seo_email_render',
        'scriviamo-seo',
        'scriviamo_seo_section',
        $classArr
    );

    add_settings_field(
        'scriviamo_seo_password',
        'Password',
        'scriviamo_seo_password_render',
        'scriviamo-seo',
        'scriviamo_seo_section',
        $classArr
    );

    add_settings_field(
        'scriviamo_seo_token',
        'Token',
        'scriviamo_seo_token_render',
        'scriviamo-seo',
        'scriviamo_seo_section'
    );

    register_setting('scriviamo_seo', 'scriviamo_seo_email', 'sanitize_email');
    register_setting('scriviamo_seo', 'scriviamo_seo_password', 'sanitize_text_field');
    register_setting('scriviamo_seo', 'scriviamo_seo_token', 'sanitize_text_field');
}
add_action('admin_init', 'scriviamo_seo_settings_init');

/*
 * Inputs for options page
 */
function scriviamo_seo_email_render() {
    if(get_option('scriviamo_seo_token', '') === ''){
    $value = get_option('scriviamo_seo_email', '');
    echo '<input type="email" id="scriviamo_seo_email" name="scriviamo_seo_email" value="' . esc_attr($value) . '" />';
    }
}

function scriviamo_seo_password_render() {
    if(get_option('scriviamo_seo_token', '') === ''){
    $value = get_option('scriviamo_seo_password', '');
    echo '<input type="password" id="scriviamo_seo_password" name="scriviamo_seo_password" value="' . esc_attr($value) . '" />';
   }
}

function scriviamo_seo_token_render() {
    $value = get_option('scriviamo_seo_token', '');
    echo '<input type="text" id="scriviamo_seo_token" name="scriviamo_seo_token" value="' . esc_attr($value) . '"  />';
}

/*
 * Options page
 */
function scriviamo_seo_options_page() {


    if(isset($_POST['scriviamo_seo_token'])){
        update_option('scriviamo_seo_token', sanitize_text_field($_POST['scriviamo_seo_token']));
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['scriviamo_seo_email']) && isset($_POST['scriviamo_seo_password'])) {

        $response = wp_remote_post('https://api.scriviamo.ai/api/login', array(
            'method'    => 'POST',
            'body'      => json_encode(array('email' => $_POST['scriviamo_seo_email'], 'password' => $_POST['scriviamo_seo_password'], 'type' => 'external', 'site'=> get_bloginfo('name') )),
            'headers'   => array('Content-Type' => 'application/json'),
        ));

        //print_r($response);
        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            echo "<div class='error'><p>Error: $error_message</p></div>";
        } else {
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body);
            if (isset($data->access_token)) {
                update_option('scriviamo_seo_token', sanitize_text_field($data->access_token));
                echo "<div class='updated'><p>Token saved successfully.</p></div>";
            } else {
                echo "<div class='error'><p>Error: Invalid response from API.</p></div>";
            }
        }
    }
    ?>
    <div class="wrap">
        <h1>Scriviamo Settings</h1>
        <form method="post" action="">
            <?php
            settings_fields('scriviamo_seo');
            do_settings_sections('scriviamo-seo');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

/*
 * Add menu link
 */
function scriviamo_seo_add_admin_menu() {
    add_options_page(
        'Scriviamo Settings',
        'Scriviamo',
        'manage_options',
        'scriviamo-seo',
        'scriviamo_seo_options_page'
    );
}
add_action('admin_menu', 'scriviamo_seo_add_admin_menu');
?>
