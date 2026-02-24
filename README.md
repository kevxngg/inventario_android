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

```
inventario_android/
├── .htaccess                    # Configuración del servidor Apache (seguridad y URLs amigables)
├── index.php                    # Front Controller: Enruta todas las peticiones hacia los controladores
│
├── assets/                      # Recursos estáticos del Frontend
│   ├── css/
│   │   ├── android-theme.css    # Hoja de estilos principal con la apariencia "Liquid" de Android
│   │   └── styles.css           # Estilos generales y complementarios del sistema
│   ├── img/                     # Almacenamiento de imágenes subidas y estáticas
│   │   ├── (Fotos de herramientas .webp, .jpg)
│   │   └── (Fotos de perfil de usuarios .png)
│   └── js/
│       ├── animations.js        # Lógica de interacciones, menú lateral, SweetAlerts y animaciones
│       ├── map-logic.js         # Lógica del mapa interactivo de geolocalización de proyectos
│       └── validations.js       # Script para validar los formularios en tiempo real
│
├── config/                      # Archivos de configuración global
│   ├── db.php                   # Credenciales y conexión a la base de datos MySQL (PDO)
│   ├── MailService.php          # Configuración y métodos de envío de correos por SMTP
│   └── parameters.php           # Definición de constantes como BASE_URL y controladores por defecto
│
├── controllers/                 # Controladores (Lógica de negocio e intermediarios)
│   ├── AdminController.php      # Gestiona todo el panel de administración (CRUDs, dashboard, reportes)
│   ├── ApiController.php        # Puntos de enlace para peticiones asíncronas (AJAX/Fetch)
│   ├── AuthController.php       # Lógica de inicio de sesión, registro y restablecimiento de claves
│   ├── HomeController.php       # Controlador principal de la página de inicio pública
│   └── UserController.php       # Gestiona el panel de los operarios (mis herramientas, reportes)
│
├── libs/                        # Dependencias de terceros
│   └── src/                     # Archivos fuente de la librería PHPMailer
│       ├── DSNConfigurator.php
│       ├── Exception.php        # Manejo de errores de correo
│       ├── OAuth.php
│       ├── OAuthTokenProvider.php
│       ├── PHPMailer.php        # Clase principal para el envío de correos
│       ├── POP3.php
│       └── SMTP.php             # Protocolo de transferencia de correo
│
├── models/                      # Modelos (Consultas a la base de datos y reglas de datos)
│   ├── AssignmentModel.php      # Lógica de asignación de herramientas a usuarios y obras
│   ├── AuditModel.php           # Registro de trazabilidad y movimientos en la caja negra
│   ├── MaintenanceModel.php     # Gestión de entradas y salidas del taller de reparación
│   ├── ProjectModel.php         # Gestión de frentes de obra (ubicaciones, clientes, estados)
│   ├── RequestModel.php         # Manejo de solicitudes de préstamo, soporte y reportes de daño
│   ├── ToolModel.php            # Catálogo principal de activos, stock, altas y bajas
│   └── UserModel.php            # Operaciones con los usuarios del sistema (roles, estados)
│
└── views/                       # Vistas (Plantillas HTML renderizadas para el cliente)
    ├── home.php                 # Landing page del sistema
    │
    ├── admin/                   # Vistas exclusivas del rol Administrador
    │   ├── audit_logs.php       # Tabla de auditoría general
    │   ├── dashboard.php        # Panel de control estadístico (Gráficas Chart.js)
    │   ├── edit_tool.php        # Formulario de modificación de activos
    │   ├── edit_user.php        # Formulario de edición de personal
    │   ├── help_desk.php        # Interfaz de chat/soporte técnico tipo WhatsApp
    │   ├── map.php              # Mapa de distribución de frentes de obra
    │   ├── print_reports.php    # Vista optimizada para la impresión de documentos
    │   ├── profile.php          # Ajustes de perfil del administrador
    │   ├── qr_catalog.php       # Visualización y generación de etiquetas QR
    │   ├── reports.php          # Bandeja de entrada de solicitudes operativas
    │   ├── tools.php            # Tabla principal del inventario general
    │   ├── users.php            # Tabla de gestión de personal
    │   └── workshop.php         # Panel de control del área de mantenimiento
    │
    ├── auth/                    # Vistas para la autenticación
    │   ├── forgot_password.php  # Petición de recuperación de clave
    │   ├── login.php            # Formulario de acceso al sistema
    │   ├── register.php         # Formulario de creación de cuenta
    │   ├── reset_password.php   # Formulario de inserción de nueva clave
    │   ├── verify.php           # Pantalla de éxito tras confirmar cuenta
    │   └── verify_recovery.php  # Validación de token por correo
    │
    ├── layouts/                 # Componentes maestros de la interfaz
    │   ├── footer.php           # Cierre de HTML y scripts finales
    │   ├── header.php           # Etiquetas <head>, navbar y barra superior
    │   └── sidebar.php          # Menú lateral de navegación según rol
    │
    └── user/                    # Vistas exclusivas del rol Operario/Usuario
        ├── catalog.php          # Vista pública del inventario disponible para pedir
        ├── dashboard.php        # Panel resumido del estado del usuario
        ├── help_desk.php        # Chat de soporte desde la vista del operario
        ├── my_tools.php         # Inventario actualmente asignado al operario
        ├── panel.php            # Vista de bienvenida/informativa secundaria
        ├── profile.php          # Ajustes de cuenta y cambio de clave del usuario
        └── report_damage.php    # Formulario para registrar pérdida o daño de un equipo

```

## ⚙️ Instalación y Configuración Local

Sigue estos pasos para desplegar el proyecto en tu entorno de desarrollo local (ej. XAMPP):

Clonar o descargar el proyecto:
Ubica la carpeta inventario_android dentro del directorio público de tu servidor web (por ejemplo, C:\xampp\htdocs\inventario_android).

**Base de Datos:**

Abre phpMyAdmin (generalmente en http://localhost/phpmyadmin).
Crea una base de datos nueva (ej. inventario_bd).

Importa el archivo .sql de tu base de datos (asegúrate de incluir la estructura de las tablas users, tools, assignments, etc.).

Configurar la conexión a MySQL:
Abre el archivo config/db.php y ajusta las credenciales de conexión:


**Ejemplo de db.php:**

```
$host = "localhost";
$dbname = "inventario_bd"; // Tu nombre de base de datos
$user = "root";
    $pass = "";
```

**Configurar Constantes Globales:**
Abre el archivo config/parameters.php y asegúrate de que la BASE_URL apunte correctamente a la ruta de tu entorno local:

```
define("BASE_URL", "http://localhost/inventario_android/");
```

**Configurar el Servicio de Correo (Opcional pero recomendado):**
Si deseas probar el restablecimiento de contraseñas y alertas, edita config/MailService.php y añade tus credenciales SMTP (por ejemplo, Gmail con contraseña de aplicación).

**Ejecución:**
Abre tu navegador web e ingresa a: http://localhost/inventario_android/. Deberías ver la pantalla de inicio o el panel de autenticación.

## 💡 Uso del Sistema

**Estructura de Enrutamiento:** El sistema utiliza la URL para determinar el controlador y la acción a ejecutar a través del index.php.

**Ejemplo:** BASE_URL?controller=Auth&action=login cargará el método login dentro del AuthController.

**Protección de Rutas:** Los controladores (AdminController y UserController) verifican al inicio si existe una sesión activa y si el rol del usuario coincide con los permisos requeridos. De lo contrario, redirigen al login.

**Componentes Reutilizables:** Las vistas cargan dinámicamente el header.php, sidebar.php y footer.php desde la carpeta layouts/ para mantener consistencia en la interfaz.

## 👨‍💻 Desarrollo

**Sistema diseñado cuidando los estándares de seguridad web:**

Contraseñas encriptadas mediante password_hash() en PHP.

Prevención de XSS y ataques de inyección de código mediante el saneamiento de variables.

Interfaz orientada a la experiencia de usuario (UX) con retroalimentación clara mediante validaciones en frontend (validations.js) y respuestas desde el servidor.
