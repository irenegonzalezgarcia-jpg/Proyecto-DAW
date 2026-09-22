<?php
namespace Core;

class EmailService {
    
    private static function saveEmail($to, $subject, $htmlContent) {
        $dir = BASE_PATH . '/storage/emails';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $id = uniqid('email_');
        $filename = $dir . '/' . $id . '.html';

        $fullHtml = "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <title>$subject</title>
            <style>
                body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
                .email-container { max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
                .header { text-align: center; border-bottom: 2px solid #efeee5; padding-bottom: 20px; margin-bottom: 20px; }
                .header h1 { color: #333; margin: 0; font-size: 24px; }
                .header p { color: #666; margin: 5px 0 0 0; }
                .content { color: #444; line-height: 1.6; }
                .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; color: #888; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='email-container'>
                <div class='header'>
                    <h1>Inspire Beauty</h1>
                    <p>Simulador de Correos Electrónicos</p>
                </div>
                <div style='background-color: #e9ecef; padding: 10px; border-radius: 4px; margin-bottom: 20px; font-size: 13px; color: #555;'>
                    <strong>Para:</strong> $to <br>
                    <strong>Asunto:</strong> $subject <br>
                    <strong>Fecha:</strong> " . date('Y-m-d H:i:s') . "
                </div>
                <div class='content'>
                    $htmlContent
                </div>
                <div class='footer'>
                    Este es un correo simulado generado por el sistema interno de Inspire Beauty para el Trabajo de Fin de Ciclo.<br>
                    &copy; " . date('Y') . " Inspire Beauty. Todos los derechos reservados.
                </div>
            </div>
        </body>
        </html>
        ";

        file_put_contents($filename, $fullHtml);
        return $id;
    }

    public static function sendPurchaseTicket($to, $clientName, $purchaseId, $total, $items, $paymentMethod) {
        $subject = "Ticket de Compra - Inspire Beauty (Pedido #$purchaseId)";
        
        $itemsHtml = "<table style='width: 100%; border-collapse: collapse; margin-top: 15px;'>
            <tr style='background-color: #f8f9fa; border-bottom: 2px solid #ddd;'>
                <th style='padding: 10px; text-align: left;'>Tratamiento</th>
                <th style='padding: 10px; text-align: right;'>Precio</th>
            </tr>";
        
        foreach ($items as $item) {
            $itemsHtml .= "
            <tr style='border-bottom: 1px solid #eee;'>
                <td style='padding: 10px;'>{$item['name']}</td>
                <td style='padding: 10px; text-align: right;'>" . number_format($item['price'], 2) . "€</td>
            </tr>";
        }
        $itemsHtml .= "
            <tr>
                <td style='padding: 10px; font-weight: bold; text-align: right;'>TOTAL:</td>
                <td style='padding: 10px; font-weight: bold; text-align: right; font-size: 1.2em; color: #28a745;'>" . number_format($total, 2) . "€</td>
            </tr>
        </table>";

        $html = "
            <h2>¡Gracias por tu compra, $clientName!</h2>
            <p>Hemos procesado tu pedido correctamente. Aquí tienes los detalles de tu ticket electrónico:</p>
            <div style='background-color: #fcfcfc; border: 1px solid #eee; padding: 15px; border-radius: 5px;'>
                <p><strong>Nº de Pedido:</strong> #$purchaseId</p>
                <p><strong>Fecha:</strong> " . date('d/m/Y H:i') . "</p>
                <p><strong>Método de Pago:</strong> $paymentMethod</p>
                $itemsHtml
            </div>
            <p style='margin-top: 20px;'>Esperamos verte pronto en nuestro centro de estética.</p>
        ";

        return self::saveEmail($to, $subject, $html);
    }

    public static function sendBookingConfirmation($to, $clientName, $treatmentName, $date, $startTime, $duration) {
        $subject = "Confirmación de Reserva de Cita - Inspire Beauty";
        
        $html = "
            <h2>¡Hola, $clientName!</h2>
            <p>Hemos recibido tu solicitud de reserva de cita online correctamente.</p>
            <p>A continuación, te detallamos la información de la cita que está <strong>pendiente de confirmación</strong> por parte del centro:</p>
            <div style='background-color: #fdfbf7; border: 1px solid #efeee5; padding: 20px; border-radius: 5px; margin: 20px 0;'>
                <ul style='list-style: none; padding: 0; margin: 0;'>
                    <li style='margin-bottom: 10px;'>✨ <strong>Tratamiento:</strong> $treatmentName</li>
                    <li style='margin-bottom: 10px;'>📅 <strong>Fecha propuesta:</strong> " . date('d/m/Y', strtotime($date)) . "</li>
                    <li style='margin-bottom: 10px;'>🕒 <strong>Hora de inicio:</strong> $startTime</li>
                    <li>⏱️ <strong>Duración estimada:</strong> $duration minutos</li>
                </ul>
            </div>
            <div style='background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; font-size: 13px; color: #856404;'>
                <strong>Políticas de Cancelación:</strong> Te recordamos que la cita podrá ser cancelada de forma gratuita siempre y cuando nos avises con al menos <strong>24 horas de antelación</strong>. En breve recibirás otro correo confirmando o proponiendo un cambio en tu cita según la disponibilidad de nuestras cabinas.
            </div>
        ";

        return self::saveEmail($to, $subject, $html);
    }

    public static function sendBookingUpdate($to, $clientName, $treatmentName, $date, $time, $status) {
        $statusLabels = [
            'confirmed' => '<span style="color: #28a745; font-weight: bold;">Confirmada</span>',
            'cancelled' => '<span style="color: #dc3545; font-weight: bold;">Cancelada</span>',
            'pending' => '<span style="color: #ffc107; font-weight: bold;">Modificada y Pendiente</span>'
        ];
        $label = $statusLabels[$status] ?? $status;
        $subject = "Actualización de tu cita - Inspire Beauty";

        $html = "
            <h2>Actualización de Estado</h2>
            <p>Hola, $clientName. El centro Inspire Beauty ha actualizado el estado de tu cita para el tratamiento <strong>$treatmentName</strong>.</p>
            <p style='font-size: 1.2em;'>El nuevo estado de tu cita es: $label</p>
            <p><strong>Fecha y hora actual:</strong> " . date('d/m/Y', strtotime($date)) . " a las $time</p>
            " . ($status === 'confirmed' ? "<p>¡Te esperamos en nuestro centro a la hora acordada!</p>" : "") . "
            " . ($status === 'cancelled' ? "<p>Lamentablemente hemos tenido que cancelar esta cita. Por favor, contáctanos o realiza una nueva reserva.</p>" : "") . "
        ";

        return self::saveEmail($to, $subject, $html);
    }
}
