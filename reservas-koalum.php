<?php
/*
Plugin Name: Plugin Reservas Javi Cotilla
Plugin URI: https://koalum.com/pruebaplugin
Description: Motor de reservas para restaurantes.
Version: 1.0
Author: Javier Cotilla Segovia
Author URI: https://koalum.com
Text Domain: reservas-koalum
*/

// Registrar Custom Post Type 'Reservas'
function koalum_register_reservas_cpt() {
    $labels = array(
        'name'                  => _x('Reservas', 'Post type general name', 'textdomain'),
        'singular_name'         => _x('Reserva', 'Post type singular name', 'textdomain'),
        'menu_name'             => _x('Reservas', 'Admin Menu text', 'textdomain'),
        'name_admin_bar'        => _x('Reserva', 'Add New on Toolbar', 'textdomain'),
        'add_new'               => __('Añadir Nueva', 'textdomain'),
        'add_new_item'          => __('Añadir Nueva Reserva', 'textdomain'),
        'new_item'              => __('Nueva Reserva', 'textdomain'),
        'edit_item'             => __('Editar Reserva', 'textdomain'),
        'view_item'             => __('Ver Reserva', 'textdomain'),
        'all_items'             => __('Todas las Reservas', 'textdomain'),
        'search_items'          => __('Buscar Reservas', 'textdomain'),
        'parent_item_colon'     => __('Reservas Padre:', 'textdomain'),
        'not_found'             => __('No se encontraron Reservas.', 'textdomain'),
        'not_found_in_trash'    => __('No se encontraron Reservas en la papelera.', 'textdomain'),
        'featured_image'        => _x('Imagen Destacada', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'textdomain'),
        'set_featured_image'    => _x('Establecer imagen destacada', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'textdomain'),
        'remove_featured_image' => _x('Eliminar imagen destacada', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'textdomain'),
        'use_featured_image'    => _x('Usar como imagen destacada', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'textdomain'),
        'archives'              => _x('Archivo de Reservas', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'textdomain'),
        'insert_into_item'      => _x('Insertar en Reserva', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'textdomain'),
        'uploaded_to_this_item' => _x('Subido a esta Reserva', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'textdomain'),
        'filter_items_list'     => _x('Filtrar lista de Reservas', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'textdomain'),
        'items_list_navigation' => _x('Navegación de lista de Reservas', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'textdomain'),
        'items_list'            => _x('Lista de Reservas', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'textdomain'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'reserva'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'editor'),
    );

    register_post_type('reserva', $args);
}
add_action('init', 'koalum_register_reservas_cpt');

// Definir la función koalum_register_settings
function koalum_register_settings() {
    register_setting('koalum_settings_group', 'koalum_settings');
    add_settings_section('koalum_main_section', 'Configuraciones Principales', 'koalum_main_section_callback', 'koalum');
    add_settings_field('koalum_field_example', 'Ejemplo de Campo', 'koalum_field_example_callback', 'koalum', 'koalum_main_section');
}

function koalum_main_section_callback() {
    echo '<p>Configuraciones principales del plugin.</p>';
}

function koalum_field_example_callback() {
    $setting = get_option('koalum_settings');
    echo "<input type='text' name='koalum_settings[example]' value='" . esc_attr($setting['example']) . "' />";
}

// Registrar metaboxes personalizados
function koalum_register_meta_boxes() {
    add_meta_box('koalum_reserva_details', 'Detalles de la Reserva', 'koalum_reserva_details_callback', 'reserva', 'normal', 'high');
}
add_action('add_meta_boxes', 'koalum_register_meta_boxes');

// Callback para mostrar los campos personalizados
function koalum_reserva_details_callback($post) {
    wp_nonce_field('koalum_save_reserva_details', 'koalum_reserva_details_nonce');
    
    $nombre = get_post_meta($post->ID, '_koalum_nombre', true);
    $email = get_post_meta($post->ID, '_koalum_email', true);
    $telefono = get_post_meta($post->ID, '_koalum_telefono', true);
    $num_personas = get_post_meta($post->ID, '_koalum_num_personas', true);
    $fecha = get_post_meta($post->ID, '_koalum_fecha', true);
    $hora = get_post_meta($post->ID, '_koalum_hora', true);
    $comentarios = get_post_meta($post->ID, '_koalum_comentarios', true);

    echo '<label for="koalum_nombre">Nombre:</label>';
    echo '<input type="text" id="koalum_nombre" name="koalum_nombre" value="' . esc_attr($nombre) . '" size="25" required />';
    
    echo '<label for="koalum_email">Email:</label>';
    echo '<input type="email" id="koalum_email" name="koalum_email" value="' . esc_attr($email) . '" size="25" required />';
    
    echo '<label for="koalum_telefono">Teléfono:</label>';
    echo '<input type="text" id="koalum_telefono" name="koalum_telefono" value="' . esc_attr($telefono) . '" size="25" required />';
    
    echo '<label for="koalum_num_personas">Número de personas:</label>';
    echo '<input type="number" id="koalum_num_personas" name="koalum_num_personas" value="' . esc_attr($num_personas) . '" size="25" min="1" required />';
    
    echo '<label for="koalum_fecha">Fecha:</label>';
    echo '<input type="date" id="koalum_fecha" name="koalum_fecha" value="' . esc_attr($fecha) . '" size="25" required />';
    
    echo '<label for="koalum_hora">Hora:</label>';
    echo '<input type="time" id="koalum_hora" name="koalum_hora" value="' . esc_attr($hora) . '" size="25" required />';
    
    echo '<label for="koalum_comentarios">Comentarios:</label>';
    echo '<textarea id="koalum_comentarios" name="koalum_comentarios" rows="4" cols="50">' . esc_textarea($comentarios) . '</textarea>';
}

// Guardar datos de metaboxes y validaciones
function koalum_save_reserva_details($post_id) {
    if (!isset($_POST['koalum_reserva_details_nonce'])) {
        return $post_id;
    }

    $nonce = $_POST['koalum_reserva_details_nonce'];

    if (!wp_verify_nonce($nonce, 'koalum_save_reserva_details')) {
        return $post_id;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    if ('reserva' != $_POST['post_type']) {
        return $post_id;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }

    $errors = [];
    
    if (empty($_POST['koalum_nombre'])) {
        $errors[] = 'El nombre es obligatorio.';
    }
    if (empty($_POST['koalum_email']) || !filter_var($_POST['koalum_email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'El email es obligatorio y debe ser válido.';
    }
    if (empty($_POST['koalum_telefono']) || !preg_match('/^[0-9\-\(\)\/\+\s]*$/', $_POST['koalum_telefono'])) {
        $errors[] = 'El teléfono es obligatorio y debe ser válido.';
    }
    if (empty($_POST['koalum_num_personas']) || !filter_var($_POST['koalum_num_personas'], FILTER_VALIDATE_INT)) {
        $errors[] = 'El número de personas es obligatorio y debe ser un número válido.';
    }
    if (empty($_POST['koalum_fecha'])) {
        $errors[] = 'La fecha es obligatoria.';
    }
    if (empty($_POST['koalum_hora'])) {
        $errors[] = 'La hora es obligatoria.';
    }

    if (!empty($errors)) {
        set_transient('koalum_reserva_errors', $errors, 30);
        remove_action('save_post', 'koalum_save_reserva_details');
        wp_update_post(['ID' => $post_id, 'post_status' => 'draft']);
        add_action('save_post', 'koalum_save_reserva_details');
        return $post_id;
    }

    $fields = [
        'koalum_nombre',
        'koalum_email',
        'koalum_telefono',
        'koalum_num_personas',
        'koalum_fecha',
        'koalum_hora',
        'koalum_comentarios',
    ];

    $data = [];
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            $data[$field] = sanitize_text_field($_POST[$field]);
            update_post_meta($post_id, '_' . $field, $data[$field]);
        }
    }

    // Guardar en la tabla personalizada
    global $wpdb;
    $table_name = $wpdb->prefix . 'reservas';

    $wpdb->replace(
        $table_name,
        [
            'ID' => $post_id,
            'nombre' => $data['koalum_nombre'],
            'email' => $data['koalum_email'],
            'telefono' => $data['koalum_telefono'],
            'num_personas' => $data['koalum_num_personas'],
            'fecha_reserva' => $data['koalum_fecha'],
            'hora_reserva' => $data['koalum_hora'],
            'comentarios' => $data['koalum_comentarios'],
            'estado' => 'pendiente',
        ]
    );
}
add_action('save_post', 'koalum_save_reserva_details');

// Mostrar errores de validación
function koalum_show_reserva_errors() {
    if ($errors = get_transient('koalum_reserva_errors')) {
        delete_transient('koalum_reserva_errors');
        echo '<div class="error"><ul>';
        foreach ($errors as $error) {
            echo '<li>' . esc_html($error) . '</li>';
        }
        echo '</ul></div>';
    }
}
add_action('admin_notices', 'koalum_show_reserva_errors');

// Agregar columnas personalizadas a la lista de reservas
function koalum_set_custom_edit_reserva_columns($columns) {
    $columns['nombre'] = __('Nombre', 'textdomain');
    $columns['email'] = __('Email', 'textdomain');
    $columns['telefono'] = __('Teléfono', 'textdomain');
    $columns['num_personas'] = __('Número de Personas', 'textdomain');
    $columns['fecha_reserva'] = __('Fecha', 'textdomain');
    $columns['hora_reserva'] = __('Hora', 'textdomain');
    return $columns;
}
add_filter('manage_reserva_posts_columns', 'koalum_set_custom_edit_reserva_columns');

// Mostrar los datos en las columnas personalizadas
function koalum_custom_reserva_column($column, $post_id) {
    switch ($column) {
        case 'nombre':
            echo esc_html(get_post_meta($post_id, '_koalum_nombre', true));
            break;
        case 'email':
            echo esc_html(get_post_meta($post_id, '_koalum_email', true));
            break;
        case 'telefono':
            echo esc_html(get_post_meta($post_id, '_koalum_telefono', true));
            break;
        case 'num_personas':
            echo esc_html(get_post_meta($post_id, '_koalum_num_personas', true));
            break;
        case 'fecha_reserva':
            echo esc_html(get_post_meta($post_id, '_koalum_fecha', true));
            break;
        case 'hora_reserva':
            echo esc_html(get_post_meta($post_id, '_koalum_hora', true));
            break;
    }
}
add_action('manage_reserva_posts_custom_column', 'koalum_custom_reserva_column', 10, 2);

// Incluir estilos y scripts
function rk_enqueue_scripts() {
    wp_enqueue_style('rk-reservas-style', plugin_dir_url(__FILE__) . 'assets/css/style.css');
    wp_enqueue_script('rk-reservas-script', plugin_dir_url(__FILE__) . 'assets/js/main.js', array('jquery'), false, true);
}
add_action('wp_enqueue_scripts', 'rk_enqueue_scripts');

function rk_enqueue_admin_scripts($hook) {
    if ($hook != 'toplevel_page_koalum-calendar') {
        return;
    }
    // FullCalendar y Moment.js
    wp_enqueue_style('fullcalendar-css', 'https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css');
    wp_enqueue_script('moment-js', 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js', array('jquery'), true);
    wp_enqueue_script('fullcalendar-js', 'https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js', array('jquery', 'moment-js'), true);
    wp_enqueue_script('calendar-setup-js', plugin_dir_url(__FILE__) . 'assets/js/calendar-setup.js', array('fullcalendar-js'), true);

    // Localizar script
    wp_localize_script('calendar-setup-js', 'reservationData', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('load_reservations_nonce')
    ));
}
add_action('admin_enqueue_scripts', 'rk_enqueue_admin_scripts');

// Agregar menús al panel de administración
function rk_add_admin_menu() {
    add_menu_page('Plugin Reservas Javi', 'Plugin Reservas Javi', 'manage_options', 'koalum', 'rk_main_menu_page');
    // add_submenu_page('koalum', 'Personalización de Emails', 'Emails', 'manage_options', 'rk-emails', 'rk_emails_submenu_page');
    // add_submenu_page('koalum', 'Configuración de Horarios', 'Horarios', 'manage_options', 'rk-settings', 'rk_settings_submenu_page');
    add_submenu_page('koalum', 'Calendario', 'Calendario', 'manage_options', 'rk_calendar_submenu_page');
}
add_action('admin_menu', 'rk_add_admin_menu');

function rk_main_menu_page() {
    echo '<h1>Bienvenido a mi Plugin de Reservas</h1>';
    // Llamar a la función de registro de configuraciones aquí si es necesario
    koalum_register_settings();
    echo '<form action="options.php" method="post">';
    settings_fields('koalum_settings_group');
    do_settings_sections('koalum');
    submit_button();
}

function rk_emails_submenu_page() {
    echo '<h1>Personalización de Emails</h1>';
    koalum_register_settings();
    echo '<form action="options.php" method="post">';
    settings_fields('koalum_settings_group');
    do_settings_sections('koalum');
    submit_button();
}

function rk_settings_submenu_page() {
    echo '<h1>Configuración de Horarios</h1>';
    // Aquí puedes agregar configuraciones específicas para horarios si es necesario
    echo '<form action="options.php" method="post">';
    settings_fields('koalum_settings_group');
    do_settings_sections('koalum');
    submit_button();
}

function rk_calendar_submenu_page() {
    echo '<div class="wrap"><h1>Calendario de Reservas</h1>';
    echo '<div id="calendar"></div>';  // Contenedor donde se cargará el calendario
    echo '</div>';
}

// Cargar archivos necesarios
include_once plugin_dir_path(__FILE__) . 'includes/form-handler.php';
include_once plugin_dir_path(__FILE__) . 'includes/installer.php';
include_once plugin_dir_path(__FILE__) . 'includes/utilities.php';
include_once plugin_dir_path(__FILE__) . 'includes/email-manager.php';
include_once plugin_dir_path(__FILE__) . 'includes/settings.php';

// Shortcode para insertar el formulario de reservas
function rk_reservas_form_shortcode() {
    ob_start();
    include(plugin_dir_path(__FILE__) . 'templates/form-template.php');
    return ob_get_clean();
}
add_shortcode('formulario_reservas', 'rk_reservas_form_shortcode');

// Hook para crear la tabla en la base de datos al activar el plugin
function rk_activate_plugin() {
    rk_install();
}
register_activation_hook(__FILE__, 'rk_activate_plugin');

// Añadir manejo para confirmación y cancelación de reservas
add_action('admin_post_confirm_reservation', 'rk_handle_confirm_reservation');
add_action('admin_post_cancel_reservation', 'rk_handle_cancel_reservation');

function rk_handle_confirm_reservation() {
    $reservation_id = isset($_GET['reservation_id']) ? intval($_GET['reservation_id']) : 0;
    if ($reservation_id) {
        rk_update_reservation_status($reservation_id, 'confirmed');
        rk_send_reservation_email($reservation_id, 'confirmed');
        wp_redirect('https://asesoran-cp23.wordpresstemporal.com/pruebaplugin/reserva-aceptada/?reservation_id=' . $reservation_id);
        exit;
    }
}

function rk_handle_cancel_reservation() {
    $reservation_id = isset($_GET['reservation_id']) ? intval($_GET['reservation_id']) : 0;
    if ($reservation_id) {
        rk_update_reservation_status($reservation_id, 'cancelled');
        rk_send_reservation_email($reservation_id, 'cancelled');
        wp_redirect('https://asesoran-cp23.wordpresstemporal.com/pruebaplugin/reserva-rechazada/?reservation_id=' . $reservation_id);
        exit;
    }
}

function rk_update_reservation_status($reservation_id, $new_status) {
    global $wpdb;
    $wpdb->update($wpdb->prefix . 'reservas', ['estado' => $new_status], ['ID' => $reservation_id]);

    // Obtener detalles de la reserva para enviar el email correcto
    $reservation = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}reservas WHERE ID = $reservation_id");
    $to = $reservation->email;
    $subject = '';
    $message = '';

    if ($new_status === 'aceptada') {
        $subject = 'Su reserva ha sido aceptada';
        $message = 'Estimado/a ' . $reservation->nombre . ",\n\nSu reserva para el " . $reservation->fecha_reserva . " a las " . $reservation->hora_reserva . " ha sido aceptada.\n\nGracias por reservar con nosotros.";
    } elseif ($new_status === 'rechazada') {
        $subject = 'Su reserva ha sido rechazada';
        $message = 'Estimado/a ' . $reservation->nombre . ",\n\nLamentamos informarle que su reserva para el " . $reservation->fecha_reserva . " a las " . $reservation->hora_reserva . " ha sido rechazada.\n\nPor favor, contacte con nosotros para más detalles.";
    }

    // Enviar el email
    if (!empty($subject) && !empty($message)) {
        wp_mail($to, $subject, $message);
    }
}

function rk_send_reservation_email($reservation_id, $status) {
    global $wpdb;
    $reservation = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}reservas WHERE ID = %d", $reservation_id));
    if ($reservation) {
        $customer_email = $reservation->email;
        $reservation_details = [
            'date' => $reservation->fecha_reserva,
            'time' => $reservation->hora_reserva,
            'people' => $reservation->num_personas
        ];
        rk_send_confirmation_email($customer_email, $reservation_id, $status, $reservation_details);
    }
}

// Agregar el handler de AJAX para administradores
add_action('wp_ajax_load_reservations', 'rk_load_reservations_callback');

function rk_load_reservations_callback() {
    check_ajax_referer('load_reservations_nonce', 'nonce');

    global $wpdb;
    $reservations = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}reservas");

    $events = array();
    foreach ($reservations as $reservation) {
        $events[] = array(
            'title' => $reservation->num_personas . ' personas - ' . $reservation->nombre,
            'start' => $reservation->fecha_reserva . 'T' . $reservation->hora_reserva,
            'allDay' => false // Define si el evento ocupa todo el día
        );
    }

    wp_send_json($events);
    wp_die(); // este llamado es necesario para terminar correctamente con las funciones de WordPress AJAX
}

// Incluir el archivo de shortcodes
include_once plugin_dir_path(__FILE__) . 'shortcodes.php';


