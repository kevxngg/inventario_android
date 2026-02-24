<?php
// Requerir los archivos de PHPMailer que acabamos de descargar
require_once __DIR__ . '/../libs/src/Exception.php';
require_once __DIR__ . '/../libs/src/PHPMailer.php';
require_once __DIR__ . '/../libs/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService {
    private $mail;

    public function __construct() {
        $this->mail = new PHPMailer(true);

        try {
            // Configuración del Servidor SMTP (Ejemplo con Gmail)
            $this->mail->isSMTP();
            $this->mail->Host       = 'smtp.gmail.com'; 
            $this->mail->SMTPAuth   = true;
            
            // =========================================================
            // ⚠️ AQUÍ DEBES PONER TU CORREO Y TU CONTRASEÑA DE APLICACIÓN
            // =========================================================
            $this->mail->Username   = 'xxxxx@gmail.com'; // Tu correo real
            $this->mail->Password   = 'abcd efgh ijkl mnop'; // Tu Contraseña de Aplicación de Google (NO tu clave normal)
            // =========================================================
            
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mail->Port       = 587;
            $this->mail->CharSet    = 'UTF-8';

            // Remitente (Quien envía)
            $this->mail->setFrom('sicoterp@gmail.com', 'SICOT ERP - Sistema de Alertas');
            
        } catch (Exception $e) {
            error_log("Error al configurar MailService: {$this->mail->ErrorInfo}");
        }
    }

    // Función para enviar un correo de Solicitud Aprobada / Rechazada
    public function sendRequestNotification($toEmail, $toName, $toolName, $status) {
        try {
            $this->mail->addAddress($toEmail, $toName);
            
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Actualización de su Solicitud de Activo - SICOT ERP';
            
            $color = ($status == 'APROBADO') ? '#198754' : '#dc3545';
            $mensajeEstado = ($status == 'APROBADO') 
                ? 'Su solicitud ha sido <strong>APROBADA</strong>. Puede pasar a la bodega principal a retirar el activo.' 
                : 'Su solicitud ha sido <strong>DENEGADA</strong> por el departamento administrativo.';

            // Plantilla HTML del correo
            $body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden;'>
                <div style='background-color: #004b87; padding: 20px; text-align: center; color: white;'>
                    <h2 style='margin: 0;'>SICOT ERP</h2>
                    <p style='margin: 5px 0 0 0; font-size: 14px;'>Notificación Automática del Sistema</p>
                </div>
                <div style='padding: 30px; background-color: #f8fafc;'>
                    <p style='font-size: 16px; color: #333;'>Hola, <strong>{$toName}</strong>:</p>
                    <p style='font-size: 16px; color: #333;'>Este es un aviso sobre su solicitud para el activo: <strong style='color: #004b87;'>{$toolName}</strong>.</p>
                    
                    <div style='background-color: white; border-left: 5px solid {$color}; padding: 15px; margin: 25px 0; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);'>
                        <h3 style='margin: 0 0 10px 0; color: {$color};'>Estado: {$status}</h3>
                        <p style='margin: 0; color: #555;'>{$mensajeEstado}</p>
                    </div>
                    
                    <p style='font-size: 14px; color: #777; border-top: 1px solid #ddd; padding-top: 15px;'>Por favor, no responda a este correo. Es generado automáticamente por el Sistema de Inventarios.</p>
                </div>
            </div>";

            $this->mail->Body = $body;
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("No se pudo enviar el correo a {$toEmail}. Error: {$this->mail->ErrorInfo}");
            return false;
        }
    }
    // NUEVA FUNCIÓN: Enviar Código de Verificación (OTP)
    public function sendVerificationCode($toEmail, $toName, $code) {
        try {
            $this->mail->clearAddresses(); // Limpiar destinatarios anteriores
            $this->mail->addAddress($toEmail, $toName);
            
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Codigo de Seguridad - Validacion de Cuenta SICOT ERP';
            
            $body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden;'>
                <div style='background-color: #004b87; padding: 20px; text-align: center; color: white;'>
                    <h2 style='margin: 0;'>SICOT ERP</h2>
                    <p style='margin: 5px 0 0 0; font-size: 14px;'>Verificación de Seguridad</p>
                </div>
                <div style='padding: 30px; background-color: #f8fafc; text-align: center;'>
                    <p style='font-size: 16px; color: #333;'>Hola, <strong>{$toName}</strong>. Bienvenido a la plataforma.</p>
                    <p style='font-size: 15px; color: #555;'>Para activar tu cuenta, ingresa el siguiente código de seguridad. <strong>Es válido solo por 10 minutos:</strong></p>
                    
                    <div style='background-color: #eef5ff; border: 2px dashed #004b87; padding: 20px; margin: 25px auto; width: max-content; border-radius: 10px;'>
                        <h1 style='margin: 0; color: #004b87; font-size: 32px; letter-spacing: 5px;'>{$code}</h1>
                    </div>
                    
                    <p style='font-size: 13px; color: #777; border-top: 1px solid #ddd; padding-top: 15px;'>Si no solicitaste este registro, ignora este correo.</p>
                </div>
            </div>";

            $this->mail->Body = $body;
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("No se pudo enviar el correo de verificación. Error: {$this->mail->ErrorInfo}");
            return false;
        }
    }

    // NUEVA FUNCIÓN: Enviar Código para Recuperar Contraseña
    public function sendPasswordRecovery($toEmail, $toName, $code) {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($toEmail, $toName);
            
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Recuperacion de Contrasena - SICOT ERP';
            
            $body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden;'>
                <div style='background-color: #dc3545; padding: 20px; text-align: center; color: white;'>
                    <h2 style='margin: 0;'>SICOT ERP</h2>
                    <p style='margin: 5px 0 0 0; font-size: 14px;'>Recuperación de Acceso</p>
                </div>
                <div style='padding: 30px; background-color: #f8fafc; text-align: center;'>
                    <p style='font-size: 16px; color: #333;'>Hola, <strong>{$toName}</strong>.</p>
                    <p style='font-size: 15px; color: #555;'>Has solicitado restablecer tu contraseña. Usa el siguiente código de seguridad (Válido por 10 minutos):</p>
                    
                    <div style='background-color: #fff5f5; border: 2px dashed #dc3545; padding: 20px; margin: 25px auto; width: max-content; border-radius: 10px;'>
                        <h1 style='margin: 0; color: #dc3545; font-size: 32px; letter-spacing: 5px;'>{$code}</h1>
                    </div>
                    
                    <p style='font-size: 13px; color: #777; border-top: 1px solid #ddd; padding-top: 15px;'>Si no solicitaste este cambio, ignora este correo. Tu cuenta está segura.</p>
                </div>
            </div>";

            $this->mail->Body = $body;
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    // =================================================================================
    // NUEVA FUNCIÓN: Enviar Respuesta Administrativa a un Reporte de Incidencia
    // =================================================================================
    public function sendIncidentReply($toEmail, $toName, $originalIssue, $adminReply) {
        try {
            $this->mail->clearAddresses(); 
            $this->mail->addAddress($toEmail, $toName);
            
            $this->mail->isHTML(true); 
            $this->mail->Subject = 'Soporte Tecnico: Resolucion de su Incidencia - SICOT ERP';
            
            $body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05);'>
                <div style='background-color: #0d6efd; padding: 20px; text-align: center; color: white;'>
                    <h2 style='margin: 0;'>SICOT ERP - Mesa de Ayuda</h2>
                    <p style='margin: 5px 0 0 0; font-size: 14px;'>Actualización de su Ticket de Soporte</p>
                </div>
                <div style='padding: 30px; background-color: #ffffff;'>
                    <p style='font-size: 16px; color: #333;'>Hola <strong>{$toName}</strong>,</p>
                    <p style='font-size: 15px; color: #555;'>El departamento administrativo ha revisado y respondido a la incidencia que reportaste recientemente en el sistema.</p>
                    
                    <div style='background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; border-radius: 5px; margin-top: 20px; margin-bottom: 20px;'>
                        <small style='color: #6c757d; text-transform: uppercase; font-weight: bold;'>Detalle Original de su Reporte:</small><br>
                        <p style='margin: 5px 0 0 0; font-style: italic; color: #495057;'>\"{$originalIssue}\"</p>
                    </div>

                    <div style='background-color: #e7f1ff; border-left: 4px solid #0d6efd; padding: 15px; border-radius: 0 5px 5px 0;'>
                        <strong style='color: #0d6efd; font-size: 14px; text-transform: uppercase;'>Respuesta Oficial / Instrucciones:</strong><br>
                        <p style='margin: 10px 0 0 0; color: #212529; font-size: 15px; line-height: 1.5;'>{$adminReply}</p>
                    </div>
                    
                    <p style='font-size: 13px; color: #adb5bd; border-top: 1px dashed #dee2e6; padding-top: 15px; margin-top: 25px;'>
                        Su ticket ha sido marcado como <strong>RESUELTO</strong>. Si el problema persiste, por favor genere un nuevo reporte desde su panel de operaciones.<br><br>
                        Este es un correo automático, por favor no lo responda.
                    </p>
                </div>
            </div>";

            $this->mail->Body = $body;
            return $this->mail->send();
        } catch (Exception $e) {
            error_log("No se pudo enviar la respuesta de incidencia a {$toEmail}. Error: {$this->mail->ErrorInfo}");
            return false;
        }
    }
}

?>
