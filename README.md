# school-frontend-php

Web frontend for scores management (Pedido 2) built with Laravel 11 + PHP 8.2.

## Features

- Login with JWT authentication (via .NET API)
- Search students by ID or name (Select2 searchable dropdown)
- View scores by student, grade, month and year
- Record individual scores (0-10 range with validation)
- Bootstrap 5 responsive UI

## Setup

```bash
# Run locally (port 8081)
make dev

# View logs
make logs

# Stop
make stop
```

## Architecture

- **Framework**: Laravel 11 (PHP 8.2)
- **Server**: Apache in Docker
- **API**: Consumes school-api-dotnet (.NET 8)
- **Deploy**: AWS ECS Fargate (via school-infra CDK)
- **Session**: File-based (no database dependency)

## Production

Deployment is managed from the `school-infra` repository:

```bash
cd school-infra && make deploy-services
```
