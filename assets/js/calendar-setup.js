jQuery(document).ready(function($) {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth', // Vista inicial del calendario mensual
        locale: 'es', // Localización del calendario, puedes cambiarlo según necesites
        buttonText: {
            today: 'Hoy',
            month: 'Mes',
            week: 'Semana',
            day: 'Día',
            list: 'Lista'
        },
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            $.ajax({
                url: ajaxurl, // `ajaxurl` es una variable global definida por WordPress
                type: 'POST',
                data: {
                    action: 'load_reservations', // La acción que maneja la carga de reservas
                    nonce: reservationData.nonce // La nonce para seguridad
                },
                success: function(response) {
                    var events = [];
                    $(response).each(function() {
                        events.push({
                            title: this.num_personas + ' personas - ' + this.nombre, // Título del evento
                            start: this.fecha_reserva + 'T' + this.hora_reserva, // Fecha y hora de inicio
                            allDay: false // Indica que el evento tiene hora específica
                        });
                    });
                    successCallback(events);
                },
                error: function() {
                    failureCallback();
                }
            });
        }
    });

    calendar.render(); // Renderiza el calendario en la página
});
