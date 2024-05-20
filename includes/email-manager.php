<?php
// Función para enviar el correo electrónico de confirmación al cliente
function rk_send_confirmation_email($customer_email, $reservation_id, $status, $reservation_details) {
    global $rk_reservation_details;
    $rk_reservation_details = $reservation_details;

    $subject = get_option('koalum_settings')['email_subject'] ?? 'Estado de su reserva';
    $headers = array('Content-Type: text/html; charset=UTF-8');

    // Elegir la plantilla de correo basada en el estado actual de la reserva
    switch ($status) {
        case 'confirmed':
            $template = get_option('rk_email_template_confirmed', 'Su reserva ha sido aceptada para [rk_date] a las [rk_time] para [rk_people] personas.');
            break;
        case 'cancelled':
            $template = get_option('rk_email_template_cancelled', 'Lamentamos informarle que su reserva para [rk_date] a las [rk_time] ha sido rechazada.');
            break;
        case 'pending': // Considerar 'pending' explícitamente para claridad
        default:
            $template = get_option('rk_email_template_pending', 'Su reserva está pendiente para [rk_date] a las [rk_time] para [rk_people] personas.');
            break;
    }

    // Reemplazar los placeholders en la plantilla
    $message = str_replace('[rk_date]', $reservation_details['date'], $template);
    $message = str_replace('[rk_time]', $reservation_details['time'], $message);
    $message = str_replace('[rk_people]', $reservation_details['people'], $message);

    wp_mail($customer_email, $subject, $message, $headers);
}

// Función para enviar un correo electrónico al administrador del sitio con enlaces para confirmar o cancelar la reserva
function rk_send_admin_notification_email($customer_email, $reservation_id) {
    global $wpdb;
    $admin_email = get_option('admin_email', get_bloginfo('admin_email'));
    $confirm_link = 'https://asesoran-cp23.wordpresstemporal.com/pruebaplugin/reserva-aceptada/?reservation_id=' . $reservation_id;
    $cancel_link = 'https://asesoran-cp23.wordpresstemporal.com/pruebaplugin/reserva-rechazada/?reservation_id=' . $reservation_id;

    // Obtener detalles de la reserva
    $reservation = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}reservas WHERE ID = %d", $reservation_id));
    if (!$reservation) {
        return; // No hacer nada si la reserva no existe
    }

    $message = "Nueva reserva de {$customer_email}.\n";
    $message .= "Detalles de la reserva:\n";
    $message .= "Fecha: {$reservation->fecha_reserva}\n";
    $message .= "Hora: {$reservation->hora_reserva}\n";
    $message .= "Número de personas: {$reservation->num_personas}\n\n";
    $message .= "<a href='{$confirm_link}'>Confirmar</a> | <a href='{$cancel_link}'>Cancelar</a>";

    wp_mail($admin_email, 'Nueva reserva recibida', $message);
}



