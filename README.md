# Salario Claro API

A Brazilian CLT payroll simulator API built with Laravel, PHP 8.3, PostgreSQL and Docker.

This is a production-minded MVP for a junior backend developer portfolio. It focuses on clean architecture, testable payroll rules, API consistency, Docker-based setup, and documented endpoints.

## Features

- Net salary calculation
- Progressive INSS calculation
- Monthly IRRF calculation
- Optional discounts
- Payroll simulation history
- Salary proposal comparison
- Versioned tax rules
- API documentation
- Automated tests

## Architecture

- **Controllers** receive HTTP requests, call application services, and return JSON responses.
- **Form Requests** validate request payloads and keep validation rules out of controllers.
- **Services** contain payroll business logic, including INSS, IRRF, tax rule resolution, and payroll orchestration.
- **DTOs** move structured payroll input and result data between layers.
- **Models** represent database tables and relationships through Eloquent.
- **Resources** serialize API responses consistently.
- **Tests** cover calculations, validation, persistence, resources, and API endpoints.

## Tech Stack

- PHP 8.3
- Laravel 11
- PostgreSQL
- Docker
- PHPUnit
- OpenAPI/Swagger

## Setup

From the `backend` directory:

```bash
cp .env.example .env
docker compose up -d
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan test
```

API base URL:

```text
http://localhost:8080/api
```

Health check:

```bash
curl http://localhost:8080/api/health
```

Expected response:

```json
{"status":"ok"}
```

## API Endpoints

| Method | Endpoint | Description |
| --- | --- | --- |
| GET | `/api/health` | API health check |
| POST | `/api/payroll/calculate` | Calculate payroll without saving |
| POST | `/api/payroll/compare` | Compare two salary scenarios |
| GET | `/api/simulations` | List saved simulations |
| POST | `/api/simulations` | Calculate and save a simulation |
| GET | `/api/simulations/{id}` | Show one saved simulation |
| DELETE | `/api/simulations/{id}` | Delete one saved simulation |
| GET | `/api/tax-rules` | List tax rules with brackets |
| GET | `/api/tax-rules/{id}` | Show one tax rule with brackets |

## Example: Calculate Payroll

`POST /api/payroll/calculate`

Request:

```json
{
  "gross_salary": 5000,
  "dependents": 0,
  "transport_discount": 100,
  "meal_discount": 250,
  "health_plan_discount": 300,
  "other_discounts": 50,
  "calculation_year": 2026
}
```

Response excerpt:

```json
{
  "gross_salary": 5000,
  "discounts": {
    "inss": 501.51,
    "irrf": 336.67,
    "transport": 100,
    "meal": 250,
    "health_plan": 300,
    "other": 50
  },
  "irrf_base": 4498.49,
  "total_discounts": 1538.18,
  "net_salary": 3461.82,
  "effective_rate": 0.307636,
  "calculation_year": 2026,
  "calculation_steps": [
    {
      "step": "INSS",
      "output": {
        "amount": 501.51
      }
    }
  ]
}
```

## Example: Compare Salaries

`POST /api/payroll/compare`

Request:

```json
{
  "first": {
    "gross_salary": 4500,
    "dependents": 0,
    "transport_discount": 0,
    "meal_discount": 0,
    "health_plan_discount": 0,
    "other_discounts": 0
  },
  "second": {
    "gross_salary": 5200,
    "dependents": 0,
    "transport_discount": 0,
    "meal_discount": 0,
    "health_plan_discount": 0,
    "other_discounts": 0
  },
  "calculation_year": 2026
}
```

Response excerpt:

```json
{
  "first": {
    "gross_salary": 4500,
    "total_discounts": 671.43,
    "net_salary": 3828.57,
    "effective_rate": 0.149207
  },
  "second": {
    "gross_salary": 5200,
    "total_discounts": 905.16,
    "net_salary": 4294.84,
    "effective_rate": 0.174069
  },
  "difference": {
    "gross_salary": 700,
    "net_salary": 466.27,
    "total_discounts": 233.73
  }
}
```

## API Documentation

The OpenAPI 3.0 specification is available at:

```text
docs/openapi.yaml
```

Open it with Swagger Editor, Redoc, Stoplight, or an IDE OpenAPI plugin. The spec includes request schemas, response schemas, validation errors, and examples.

## Deploy on Render

Use a Render **Web Service** with the **Docker** runtime. The project includes a root `Dockerfile` for Render deployment and keeps the existing local `docker-compose.yml` setup unchanged.

Render settings:

| Setting | Value |
| --- | --- |
| Service type | Web Service |
| Runtime / Language | Docker |
| Dockerfile Path | `./Dockerfile` |
| Docker Context | `.` |
| Build command | Leave empty; Render builds the Dockerfile |
| Start command / Docker command | Leave empty; the Dockerfile starts Laravel |
| Health check path | `/api/health` |

Required environment variables:

```text
APP_NAME=Salario Claro API
APP_ENV=production
APP_KEY=base64:your-generated-app-key
APP_DEBUG=false
APP_URL=https://your-render-service.onrender.com
APP_TIMEZONE=America/Sao_Paulo
APP_LOCALE=pt_BR
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=pt_BR
LOG_CHANNEL=stderr
LOG_LEVEL=info
DB_CONNECTION=pgsql
DB_HOST=your-render-postgres-host
DB_PORT=5432
DB_DATABASE=your-render-postgres-database
DB_USERNAME=your-render-postgres-user
DB_PASSWORD=your-render-postgres-password
CACHE_STORE=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
MAIL_MAILER=log
```

Render provides `PORT` automatically. The Dockerfile starts Laravel with `php artisan serve --host=0.0.0.0 --port=${PORT:-10000}`.

Generate `APP_KEY` before configuring Render:

```bash
php artisan key:generate --show
```

After the first deploy, run the database migrations and seeders from a Render shell or one-off job:

```bash
php artisan migrate --seed --force
```

## Testing

Run the full test suite:

```bash
docker compose exec app php artisan test
```

The tests cover:

- INSS progressive calculation
- IRRF monthly calculation
- Payroll orchestration
- Tax rule resolution
- Form request validation
- API resources
- Simulation persistence
- Payroll comparison
- Tax rule endpoints

## Future Improvements

- Authentication
- PDF export
- 13th salary calculation
- Vacation calculation
- FGTS informational calculation
- Admin panel for tax rules
