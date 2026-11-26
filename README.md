# Documentación Técnica Oficial - PRIME FITNESS
**Arquitectura MVC | Programación Orientada a Objetos (POO) | Principios SOLID | Composer Autoload | AJAX (Fetch API) | SweetAlert2 | Side Modals (Offcanvas) | PDO MySQL**

---

## 📌 1. Visión General del Sistema

**PRIME FITNESS** es una plataforma web modular desarrollada en **PHP 8** bajo arquitectura **MVC** y principios **SOLID**, dividida en dos entornos independientes:

1. **Panel Administrativo (`users`):**
   * **Login Split-Screen con Carrusel CSS:** Formulario de acceso y carrusel animado por GPU (`@keyframes`) con 3 imágenes en transición continua.
   * **Registro Público y Recuperación Primitiva de Contraseña:** Creación de cuenta y restablecimiento directo por correo con confirmación y cifrado `BCRYPT`.
   * **Módulo 1 - Inicio (Analíticas de Negocio):** Tarjetas con métricas en tiempo real:
     1. *Administradores Creados* (tabla `users`).
     2. *Clientes Registrados* (tabla `clientes`).
     3. *Pedidos Solicitados* (`0` preparado para escalabilidad).
     4. *Ingresos del Mes* (`$0.00`).
     5. *Ingresos Totales Adquiridos* (`$0.00`).
   * **Módulo 2 - Admin (CRUD de Administradores):** Gestión asíncrona de usuarios administradores (`US-001`) con **Side Modal (Panel Lateral Deslizable)** para ver ficha de información, registrar y editar, más eliminación con **SweetAlert2**.
   * **Módulo 3 - Clientes (CRUD de Clientes):** Gestión administrativa completa de la cartera de clientes (`CL-001`) mediante **Side Modal** y **AJAX**.
   * **Diseño 100% Responsivo:** Menú lateral colapsable con botón **Hamburguesa (`☰`)** y overlay difuminado para dispositivos móviles y tablets.

2. **Portal de Clientes (`clientes`):**
   * **Login de Clientes Split-Screen:** Diseño armónico con **1 sola imagen fija** (sin transición) exclusivo para clientes.
   * **Dashboard de Clientes (Mi Historial de Pedidos):** Muestra el contador de *Pedidos Solicitados (`0`)* y la tabla de historial de pedidos del cliente autenticado.

---

## 🏛️ 2. Diagrama de Arquitectura MVC e Inversión de Dependencias (SOLID)

```
                             PETICIÓN HTTP / AJAX
                                      │
                                      ▼
               [ index.php ] (Front Controller & Composition Root)
                     │
                     ├── 1. Carga vendor/autoload.php (Composer)
                     ├── 2. Obtiene PDO desde Database::getConnection()
                     ├── 3. Instancia UserModel($db) y ClientModel($db)
                     └── 4. Inyecta las interfaces en UserController y ClientController
                                      │
            ┌─────────────────────────┴─────────────────────────┐
            ▼                                                   ▼
 [ UserController (Admin) ]                        [ ClientController (Clientes) ]
            │                                                   │
            ▼                                                   ▼
 [ UserModelInterface ]                            [ ClientModelInterface ]
            ▲                                                   ▲
            │ (Implementa)                                      │ (Implementa)
   [ model/userModel.php ]                           [ model/clientModel.php ]
            │                                                   │
            └─────────────────────────┬─────────────────────────┘
                                      ▼
                      [ config/dataBase.php ] (Singleton PDO)
                                      │
                                      ▼
                     [ MySQL: sistema_db (users, clientes) ]
```

---

## 🧩 3. Principios SOLID Aplicados

| Principio | Nombre | Implementación en PRIME FITNESS |
| :---: | :--- | :--- |
| **S** | **Single Responsibility** *(Responsabilidad Única)* | • `dataBase.php`: Solo gestiona la conexión PDO.<br>• `userModel.php` / `clientModel.php`: Solo ejecutan consultas SQL.<br>• `UserController.php` / `ClientController.php`: Solo validan peticiones HTTP y responden vistas o JSON.<br>• `view/*.php`: Solo presentación visual HTML5/Bootstrap.<br>• `assets/js/*.js`: Solo lógica asíncrona AJAX y DOM. |
| **O** | **Open/Closed** *(Abierto / Cerrado)* | Se agregó todo el módulo de `Clientes` implementando `ClientModelInterface` y `ClientController` sin alterar la estructura base del sistema. |
| **L** | **Liskov Substitution** *(Sustitución de Liskov)* | Cualquier clase que implemente `UserModelInterface` o `ClientModelInterface` puede reemplazar a los modelos actuales sin afectar a los controladores. |
| **I** | **Interface Segregation** *(Segregación de Interfaces)* | Se separaron las interfaces `UserModelInterface` y `ClientModelInterface` para que cada entidad tenga únicamente los métodos que le corresponden. |
| **D** | **Dependency Inversion** *(Inversión de Dependencias)* | **Los controladores NO instancian modelos con `new`**. Reciben `UserModelInterface` y `ClientModelInterface` por su constructor desde el *Composition Root* (`index.php`). |

---

## 🗺️ 4. Mapa Completo de Rutas (`index.php`)

Todas las rutas pasan por el Front Controller `index.php?action=<ruta>`:

### 4.1. Rutas de Vistas Administrativas (HTML)
| Ruta (`?action=...`) | Método HTTP | Controlador y Método | Vista que Renderiza | Descripción |
| :--- | :---: | :--- | :--- | :--- |
| `login` *(o raíz)* | `GET` | `UserController::showLogin()` | `view/login.php` | Login Admin con carrusel de 3 imágenes |
| `registro` | `GET` | `UserController::showRegistro()` | `view/registro.php` | Formulario público para crear cuenta Admin |
| `recuperar` | `GET` | `UserController::showRecuperar()` | `view/recuperar.php` | Formulario de recuperación primitiva de clave |
| `inicio` / `dashboard` | `GET` | `UserController::showInicio()` | `view/inicio.php` | Módulo 1: Dashboard con las 5 tarjetas analíticas |
| `admin` / `usuarios` | `GET` | `UserController::showAdmin()` | `view/usuarios.php` | Módulo 2: Tabla y Side Modal de Administradores |
| `clientes` | `GET` | `ClientController::showAdminClientes()` | `view/clientes.php` | Módulo 3: Tabla y Side Modal de Clientes |

### 4.2. Rutas AJAX de Autenticación, Recuperación y CRUD de Administradores
| Ruta (`?action=...`) | Método HTTP | Controlador y Método | Respuesta | Descripción |
| :--- | :---: | :--- | :---: | :--- |
| `login_auth` | `POST` | `UserController::login()` | `JSON` | Autentica admin y crea `$_SESSION['user']` |
| `logout` | `GET` | `UserController::logout()` | `Redirect` | Destruye la sesión admin y redirige a `login` |
| `restablecer_clave` | `POST` | `UserController::restablecerClave()` | `JSON` | Cambia la contraseña validando correo y confirmación |
| `listar` | `GET` | `UserController::listar()` | `JSON` | Retorna todos los administradores de `users` |
| `obtener&id={id}` | `GET` | `UserController::obtener()` | `JSON` | Retorna los datos de un administrador para el Side Modal |
| `registrar` / `registrar_publico` | `POST` | `UserController::registrar()` | `JSON` | Crea un nuevo administrador con hash `BCRYPT` |
| `actualizar` | `POST` | `UserController::actualizar()` | `JSON` | Actualiza datos y clave opcional del administrador |
| `eliminar` | `POST` | `UserController::eliminar()` | `JSON` | Elimina un administrador por ID |

### 4.3. Rutas del Portal de Clientes y CRUD de Clientes (Vistas y AJAX)
| Ruta (`?action=...`) | Método HTTP | Controlador y Método | Vista / Respuesta | Descripción |
| :--- | :---: | :--- | :---: | :--- |
| `cliente_login` | `GET` | `ClientController::showLogin()` | `view/cliente_login.php` | Login de Clientes con 1 sola imagen fija |
| `cliente_login_auth` | `POST` | `ClientController::login()` | `JSON` | Autentica cliente y crea `$_SESSION['client']` |
| `cliente_dashboard` | `GET` | `ClientController::showDashboard()` | `view/cliente_dashboard.php` | Portal del Cliente con contador e historial de pedidos en `0` |
| `cliente_logout` | `GET` | `ClientController::logout()` | `Redirect` | Cierra la sesión del cliente y redirige a `cliente_login` |
| `cliente_listar` | `GET` | `ClientController::listar()` | `JSON` | Lista todos los clientes en el módulo Admin |
| `cliente_obtener&id={id}` | `GET` | `ClientController::obtener()` | `JSON` | Obtiene detalle de un cliente para el Side Modal |
| `cliente_registrar` | `POST` | `ClientController::registrar()` | `JSON` | Registra un nuevo cliente en la tabla `clientes` |
| `cliente_actualizar` | `POST` | `ClientController::actualizar()` | `JSON` | Actualiza datos de un cliente desde el Side Modal |
| `cliente_eliminar` | `POST` | `ClientController::eliminar()` | `JSON` | Elimina un cliente con confirmación SweetAlert2 |

---

## 📂 5. Estructura Completa de Archivos del Proyecto

```plaintext
Sistema/
├── assets/
│   ├── css/
│   │   └── styles.css               # Estilos PRIME FITNESS, Side Drawer y Media Queries Responsivas
│   ├── img/
│   │   ├── login1.jfif              # Imagen 1 (Carrusel Admin y Fija en Login Clientes)
│   │   ├── login2.jfif              # Imagen 2 (Carrusel Admin)
│   │   └── login3.jfif              # Imagen 3 (Carrusel Admin)
│   └── js/
│       ├── app.js                   # AJAX CRUD de Administradores + Side Modal
│       ├── clientes.js              # AJAX CRUD de Clientes + Side Modal
│       ├── login.js                 # AJAX Login de Administradores + SweetAlert2
│       ├── cliente_login.js         # AJAX Login de Clientes + SweetAlert2
│       ├── registro.js              # AJAX Registro Público + SweetAlert2
│       ├── recuperar.js             # AJAX Recuperación Primitiva + SweetAlert2
│       └── sidebar.js               # Controlador del Menú Hamburguesa Responsivo
├── config/
│   └── dataBase.php                 # Conexión PDO Singleton a MySQL
├── controller/
│   ├── UserController.php           # Controlador de Administradores y Analíticas
│   └── ClientController.php         # Controlador del Portal y CRUD de Clientes
├── model/
│   ├── UserModelInterface.php       # Interfaz SOLID para Administradores
│   ├── userModel.php                # Modelo PDO para tabla 'users'
│   ├── ClientModelInterface.php     # Interfaz SOLID para Clientes
│   └── clientModel.php              # Modelo PDO para tabla 'clientes'
├── view/
│   ├── login.php                    # Vista Login Admin (Carrusel 3 imágenes)
│   ├── registro.php                 # Vista Registro Público Admin
│   ├── recuperar.php                # Vista Recuperación Primitiva
│   ├── inicio.php                   # Vista Módulo 1: Inicio (5 Cards de Analíticas)
│   ├── usuarios.php                 # Vista Módulo 2: Admin Usuarios + Side Modal
│   ├── clientes.php                 # Vista Módulo 3: Admin Clientes + Side Modal
│   ├── cliente_login.php            # Vista Login Clientes (1 imagen fija)
│   └── cliente_dashboard.php        # Vista Dashboard Clientes (Historial Pedidos en 0)
├── vendor/                          # Autoloader generado por Composer
├── composer.json                    # Configuración de dependencias y classmap PSR-4
├── database.sql                     # Script SQL con tablas 'users' y 'clientes'
├── index.php                        # Front Controller, Composition Root y Enrutador
└── README.md                        # Documentación técnica completa
```

---

## 🚀 6. Guía de Ejecución y Credenciales de Prueba

1. **Importar Base de Datos:**
   Importa `database.sql` en phpMyAdmin o consola MySQL (`sistema_db`).
2. **Levantar Servidor Local:**
   ```powershell
   php -S localhost:8000
   ```
3. **Accesos Directos y Credenciales Demo:**
   * **Login Administrativo:** [http://localhost:8000/index.php?action=login](http://localhost:8000/index.php?action=login)
     * **Correo:** `admin@correo.com`
     * **Clave:** `123456`
   * **Portal de Clientes:** [http://localhost:8000/index.php?action=cliente_login](http://localhost:8000/index.php?action=cliente_login)
     * **Correo:** `cliente@correo.com`
     * **Clave:** `123456`
