# ProyectoIAW
### Hecho por: Noel Ballester Baños y Ángela Navarro Nieto. 
Hemos desarrollado una aplicación web en PHP puro que permite gestionar incidencias, donde se incluyen los requisitos mínimos y algunos extras.

Esta aplicación permite: 
- Iniciar sesión con usuario y contraseña.
- Crear, ver, editar y borrar incidencias.
- Buscar y paginar el listado de incidencias.
- Ver el detalle de cada incidencia.
- Guardar una preferencia visual en un cookie. (Tema oscuro/claro).
- Proteger todas las ruts privadas con sesión.
- Validar formularios en el servidor.
- Evitar reenvíos con PRG tras POST.
- Usar tokens CSRF en todos los formularios.
- Escapar salidas para prevenir XSS.
- Conectar a la base de datos con PDO y SQL preparado.
- Borrar con transacción y registrar en tabla de auditoría.

## Estructura del proyecto.
app/
├── pdo.php              # Conexión PDO
├── auth.php             # Sesión y protección
├── csrf.php             # Tokens CSRF
├── utils.php            # Validaciones y helpers

public/
├── login.php            # Login
├── logout.php           # Logout
├── index.php            # Panel principal
├── tickets_list.php     # Listado con búsqueda y paginación
├── tickets_form.php     # Alta y edición
├── tickets_show.php     # Detalle
├── tickets_delete.php   # Borrado con auditoría
├── preferencias.php     # Tema visual por cookie

sql/
├── schema.sql           # Tablas e índices
├── seed.sql             # Datos de ejemplo (≥50)

uploads/                 # Si hay adjuntos
README.md

## Extras añadidos: 

## Cómo arrancarlo en Ubuntu
git clone https://github.com/NoelBallester/proyectoIAW.git
cd proyecyoIAW

--- 
## Base de datos
La base de datos se llama "iaw" y está creada en MySQL. 

### Archivos SQL
- ''
- ''

### Cómo importar la base de datos 
En bash:
mysql -u root -p 
Usuario:
