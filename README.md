# Banco 3

Aplicacion PHP conectada a PostgreSQL en Neon mediante PDO.

## Requisitos

- PHP 8.1 o superior.
- Extensiones `pdo` y `pdo_pgsql`.
- Un servidor web que publique esta carpeta.

En Railway, el `Dockerfile` instala PHP 8.3, Apache y `pdo_pgsql`
automaticamente. Configura `/Banco 3` como Root Directory y registra las
variables de `.env` en la pestana Variables del servicio.

La configuracion privada vive en `.env`, que esta excluido de Git. El esquema
de la base se encuentra en `database_postgresql.sql`.

## API para el dashboard

Estado:

```http
GET /api/v1/health.php
```

Informacion financiera por cedula:

```http
POST /api/v1/dashboard.php
Content-Type: application/json

{
  "cedula": "0102030405",
  "transaction_limit": 20
}
```

La respuesta mantiene el mismo contrato de Banco 1 y Banco 2: cliente, resumen
mensual, cuentas, actividad de siete dias y movimientos recientes.

Se puede proteger la integracion definiendo `DASHBOARD_API_KEY` y enviando el
encabezado `X-API-Key`.
