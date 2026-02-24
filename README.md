# 📦 Sistema de Gestión de Inventario - Inventario Android

Sistema integral de gestión y control de inventario desarrollado en PHP y MySQL bajo el patrón de arquitectura **MVC (Modelo-Vista-Controlador)**. Este proyecto está diseñado para administrar usuarios, herramientas, asignaciones a proyectos, mantenimientos y generar reportes de manera eficiente, brindando paneles de control separados para Administradores y Usuarios estándar.

Desarrollado como parte del proyecto formativo para el programa de Tecnólogo en Análisis y Desarrollo de Software (ADSO) del SENA.



## 🚀 Características Principales

El sistema está dividido en dos módulos principales según el rol del usuario, además de contar con un sistema de autenticación seguro:

### 🔐 Autenticación y Seguridad
 **Inicio de sesión y Registro**: Acceso seguro validando roles de usuario.
 **Recuperación de contraseñas**: Integración nativa con `PHPMailer` para enviar tokens de recuperación vía correo electrónico.
 **Verificación de cuentas**: Confirmación de usuarios a través de enlaces enviados por email.
 **Trazabilidad (Logs)**: Registro de auditoría para rastrear las acciones críticas realizadas dentro del sistema.

### 👑 Módulo de Administrador
 **Dashboard Estadístico**: Vista general con métricas de herramientas, mantenimientos y usuarios.
 **Gestión de Usuarios**: Creación, edición, listado y desactivación de cuentas de operarios/usuarios.
 **Catálogo y Herramientas**: CRUD completo de herramientas. Generación y gestión de **Códigos QR** para el etiquetado e identificación rápida de cada activo.
 **Taller y Mantenimiento**: Control del ciclo de vida de las herramientas que ingresan a reparación.
 **Asignaciones y Proyectos**: Asignación de inventario específico a distintos proyectos en curso.
 **Geolocalización (Mapa)**: Seguimiento o visualización de ubicaciones mediante integración de mapas.
 **Reportes**: Generación e impresión de informes detallados sobre el estado del inventario y auditorías.
 **Mesa de Ayuda (Help Desk)**: Soporte y resolución de tickets o reportes de daños emitidos por los operarios.

### 👷 Módulo de Usuario (Operario)
 **Panel de Control**: Vista simplificada de las herramientas asignadas actualmente.
 **Catálogo Público**: Exploración de las herramientas disponibles para solicitud.
 **Mis Herramientas**: Seguimiento de los equipos bajo la responsabilidad del usuario.
 **Reporte de Daños**: Formulario dedicado para reportar averías o pérdidas de equipos.
 **Mesa de Ayuda**: Canal directo para comunicarse con los administradores en caso de incidencias.

---

## 🛠️ Tecnologías Utilizadas

 **Backend**: PHP 8.x (Arquitectura MVC pura, sin frameworks).
 **Base de Datos**: MySQL (Consultas preparadas con PDO para evitar inyección SQL).
 **Frontend**: HTML5, CSS3 (Estilos propios y diseño adaptable), JavaScript (Validaciones, animaciones y lógica de mapas).
 **Librerías Externas**: 
 `PHPMailer`: Para el envío de notificaciones y correos transaccionales por SMTP.



## 📂 Estructura del Proyecto

El código fuente está organizado siguiendo estrictamente el patrón **Modelo-Vista-Controlador (MVC)**, garantizando la escalabilidad y el mantenimiento:

```text
inventario_android/
├── assets/                 # Recursos estáticos del frontend
│   ├── css/                # Hojas de estilo (styles.css, android-theme.css)
│   ├── img/                # Imágenes del sistema, logos, fotos de herramientas y usuarios
│   └── js/                 # Scripts de frontend (animations.js, map-logic.js, validations.js)
├── config/                 # Configuraciones globales
│   ├── db.php              # Conexión a la base de datos MySQL (PDO)
│   ├── MailService.php     # Configuración y envío de correos (Wrapper de PHPMailer)
│   └── parameters.php      # Constantes globales (URL base, controladores por defecto)
├── controllers/            # Lógica de negocio y enrutamiento
│   ├── AdminController.php # Lógica del panel de administración
│   ├── ApiController.php   # Endpoints para peticiones asíncronas (AJAX/Fetch)
│   ├── AuthController.php  # Lógica de login, registro, recuperación y verificación
│   ├── HomeController.php  # Controlador de la página de inicio o landing
│   └── UserController.php  # Lógica del panel de operarios
├── libs/                   # Dependencias de terceros
│   └── src/                # Archivos de PHPMailer (PHPMailer.php, SMTP.php, Exception.php...)
├── models/                 # Lógica de base de datos (Consultas CRUD)
│   ├── AssignmentModel.php # Modelo de asignaciones a proyectos
│   ├── AuditModel.php      # Modelo de registros de auditoría
│   ├── MaintenanceModel.php# Modelo de control de taller y reparación
│   ├── ProjectModel.php    # Modelo de proyectos
│   ├── RequestModel.php    # Modelo de solicitudes de equipos
│   ├── ToolModel.php       # Modelo principal del inventario (herramientas)
│   └── UserModel.php       # Modelo de gestión de usuarios y credenciales
├── views/                  # Vistas HTML renderizadas
│   ├── admin/              # Vistas exclusivas del administrador (dashboard, map, qr_catalog, tools...)
│   ├── auth/               # Vistas de autenticación (login, register, forgot_password...)
│   ├── layouts/            # Plantillas maestras (header, footer, sidebar)
│   ├── user/               # Vistas exclusivas del operario (dashboard, my_tools, report_damage...)
│   └── home.php            # Página principal del sistema
└── index.php               # Front Controller (Punto de entrada único que enruta las peticiones)




⚙️ Instalación y Configuración Local

Sigue estos pasos para desplegar el proyecto en tu entorno de desarrollo local (ej. XAMPP):

    Clonar o descargar el proyecto:
    Ubica la carpeta inventario_android dentro del directorio público de tu servidor web (por ejemplo, C:\xampp\htdocs\inventario_android).

    Base de Datos:

        Abre phpMyAdmin (generalmente en http://localhost/phpmyadmin).

        Crea una base de datos nueva (ej. inventario_bd).

        Importa el archivo .sql de tu base de datos (asegúrate de incluir la estructura de las tablas users, tools, assignments, etc.).

    Configurar la conexión a MySQL:
    Abre el archivo config/db.php y ajusta las credenciales de conexión:
 

    // Ejemplo de db.php
    $host = "localhost";
    $dbname = "inventario_bd"; // Tu nombre de base de datos
    $user = "root";
    $pass = "";

    Configurar Constantes Globales:
    Abre el archivo config/parameters.php y asegúrate de que la BASE_URL apunte correctamente a la ruta de tu entorno local:
  

    define("BASE_URL", "http://localhost/inventario_android/");

    Configurar el Servicio de Correo (Opcional pero recomendado):
    Si deseas probar el restablecimiento de contraseñas y alertas, edita config/MailService.php y añade tus credenciales SMTP (por ejemplo, Gmail con contraseña de aplicación).

    Ejecución:
    Abre tu navegador web e ingresa a: http://localhost/inventario_android/. Deberías ver la pantalla de inicio o el panel de autenticación.

💡 Uso del Sistema

    Estructura de Enrutamiento: El sistema utiliza la URL para determinar el controlador y la acción a ejecutar a través del index.php.

        Ejemplo: BASE_URL?controller=Auth&action=login cargará el método login dentro del AuthController.

    Protección de Rutas: Los controladores (AdminController y UserController) verifican al inicio si existe una sesión activa y si el rol del usuario coincide con los permisos requeridos. De lo contrario, redirigen al login.

    Componentes Reutilizables: Las vistas cargan dinámicamente el header.php, sidebar.php y footer.php desde la carpeta layouts/ para mantener consistencia en la interfaz.

👨‍💻 Desarrollo

Sistema diseñado cuidando los estándares de seguridad web:

    Contraseñas encriptadas mediante password_hash() en PHP.

    Prevención de XSS y ataques de inyección de código mediante el saneamiento de variables.

    Interfaz orientada a la experiencia de usuario (UX) con retroalimentación clara mediante validaciones en frontend (validations.js) y respuestas desde el servidor.
