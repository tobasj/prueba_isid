## Plataforma de Cursos (API + Front Vue)

API REST en Laravel con arquitectura hexagonal, OAuth2 (Laravel Passport, password grant), soft deletes, caché de rating por curso, documentación OpenAPI (l5-swagger) y un mini front Vue+Vite para probar.

### Requisitos
- PHP 8.3, Composer
- Node 20, npm
- MySQL 8

### Configuración
1) Copia `.env.example` a `.env` y ajusta:
```
APP_URL=http://127.0.0.1:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=tu_db
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password
PASSPORT_PASSWORD_CLIENT_ID=...
PASSPORT_PASSWORD_CLIENT_SECRET=...
```
2) Instala dependencias:
```
composer install
npm install
```
3) Genera key y migra/seed:
```
php artisan key:generate
php artisan migrate --seed
```
4) Passport password grant:
```
php artisan passport:client --password --name "API Password Client"
```
Rellena en `.env` los valores de `client_id` y `client_secret`.

### Ejecutar en local
En dos terminales:
```
php artisan serve --host=127.0.0.1 --port=8000
npm run dev
```
Abre `http://127.0.0.1:8000/` (Laravel sirve la vista que monta el front Vue; no abras directamente 5173).

### Arquitectura (hexagonal)
- Domain: contratos (Ports) e interfaces.
- Application: casos de uso (p.ej. `IssuePasswordToken`, `RegisterUser`).
- Infrastructure: adaptadores (p.ej. `PassportTokenIssuer`).
- Delivery: HTTP (controllers, requests, resources).

### Autenticación
- OAuth2 password grant via Passport.
- Endpoints: `POST /api/register`, `POST /api/login`.
- Roles seeded: `student`, `instructor`.

### Modelado y características
- Cursos, lecciones, comentarios, favoritos.
- Soft deletes en users, courses, lessons, comments, favorites.
- Rating cache por curso (`average_rating`, `ratings_count`) recalculado vía `CommentObserver`.
- Validaciones con Form Requests; reglas de password robustas; regla `InstructorRole` para validar instructor.

### Front Vue (resources/js/App.vue)
- Login/registro (elige rol).
- Listado de cursos, marcar favoritos, añadir comentarios, crear curso (rol instructor) con lecciones.
- Axios con `Authorization` bearer desde localStorage; base URL `http://127.0.0.1:8000`.

### Documentación OpenAPI
- Generar: `php artisan l5-swagger:generate`
- Por defecto se expone en `public/docs` (revisa config `config/l5-swagger.php`).

### Tests
```
php artisan test
```
Tests de features: auth/cursos/comentarios/favoritos/rating y soft deletes.

### Despliegue (resumen)
- Workflow GitHub Actions `.github/workflows/deploy.yml` (push a master) ejecuta tests, build y despliegue por SSH.
- Variables/secretos: `SSH_HOST`, `SSH_USERNAME`, `SSH_PASSWORD` (o `SSH_KEY`), `ENV_FILE` o variables de APP/DB/PASSPORT/APP_KEY.
- Si usas Docker, construye/pushea imagen (ver `Dockerfile`) y en el servidor haz `docker pull` + `docker run` con `--env-file`.

### Docker (rápido)
```
docker build -t cursos-app .
docker run --env-file .env -p 8000:8000 cursos-app
```

### Rutas principales (API)
- Auth: `POST /api/register`, `POST /api/login`
- Cursos: `GET/POST /api/courses`, `GET/PUT/PATCH/DELETE /api/courses/{course}`
- Lecciones: incluidas en payload de cursos
- Comentarios: `GET/POST /api/courses/{course}/comments`, `PUT/PATCH/DELETE /api/courses/{course}/comments/{comment}`
- Favoritos: `POST/DELETE /api/courses/{course}/favorite`
- Instructores: `GET /api/instructors`

### Notas
- Usa encabezado `Accept: application/json` en llamadas.
- `APP_URL` debe incluir host/puerto reales para evitar redirecciones raras.
- Después de cambiar `.env`, ejecuta `php artisan config:clear`.
