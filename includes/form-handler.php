<?php
// Función para manejar el envío de formularios de reserva
function rk_handle_reservation_submission() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'reservas';

        // Validaciones en el servidor
        $nombre = sanitize_text_field($_POST['nombre']);
        $email = sanitize_email($_POST['email']);
        $telefono = sanitize_text_field($_POST['telefono']);
        $fecha_reserva = sanitize_text_field($_POST['fecha_reserva']);
        $hora_reserva = sanitize_text_field($_POST['hora_reserva']);
        $num_personas = intval($_POST['num_personas']);
        $comentarios = sanitize_textarea_field($_POST['comentarios']);

        // Verificar que el nombre no exceda los 40 caracteres
        if (strlen($nombre) > 40) {
            wp_die('El nombre no debe exceder los 40 caracteres.');
        }

        // Verificar que el número de teléfono tenga exactamente 9 dígitos
        if (!preg_match('/^\d{9}$/', $telefono)) {
            wp_die('El número de teléfono debe tener exactamente 9 dígitos.');
        }

        // Verificar que el email tenga el formato correcto
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            wp_die('Formato de email inválido.');
        }

        // Verificar el número máximo de personas
        if ($num_personas < 1 || $num_personas > 10) {
            wp_die('El número de personas debe estar entre 1 y 10.');
        }

        // Verificar el número máximo de palabras en comentarios
        if (str_word_count($comentarios) > 100) {
            wp_die('Los comentarios no deben exceder las 100 palabras.');
        }

        // Insertar los datos de la reserva en la base de datos con estado 'pendiente'
        $wpdb->insert($table_name, [
            'nombre' => $nombre,
            'email' => $email,
            'telefono' => $telefono,
            'fecha_reserva' => $fecha_reserva,
            'hora_reserva' => $hora_reserva,
            'num_personas' => $num_personas,
            'comentarios' => $comentarios,
            'estado' => 'pendiente',  // Asegurar que el estado inicial es 'pendiente'
        ], [
            '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s'
        ]);

        $reservation_id = $wpdb->insert_id;
        $reservation_details = [
            'date' => $fecha_reserva,
            'time' => $hora_reserva,
            'people' => $num_personas
        ];

        // Establecer los detalles de la reserva para usar en los shortcodes
        rk_set_reservation_details($reservation_details);

        // Enviar correo de confirmación de reserva pendiente
        rk_send_confirmation_email($email, $reservation_id, 'pending', $reservation_details);

        // Enviar correo de notificación al administrador
        rk_send_admin_notification_email($email, $reservation_id);
    }
}
add_action('init', 'rk_handle_reservation_submission');
