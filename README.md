## Projekt ALab

Ten projekt składa się z dwóch repozytoriów:

- Backend: [https://github.com/meamea21/alab.git](https://github.com/meamea21/alab.git)
- Frontend: [https://github.com/meamea21/frontend.git](https://github.com/meamea21/frontend.git)


## Instrukcja uruchomienia (Linux)

1. Utwórz katalogi `backend` i `frontend`.
2. Sklonuj repozytoria:

```bash
cd backend
git clone https://github.com/meamea21/alab.git
cd ../frontend
git clone https://github.com/meamea21/frontend.git
```

3. W katalogu nadrzędnym utwórz plik `docker-compose.yml` o następującej zawartości:
```yaml
version: "3.8"

services:
  backend:
    build:
      context: ./backend/alab
      dockerfile: Dockerfile.backend
    container_name: laravel_backend
    ports:
      - "8000:8000"
    depends_on:
      - db
    environment:
      - DB_CONNECTION=pgsql
      - DB_HOST=db
      - DB_PORT=5432
      - DB_DATABASE=alab
      - DB_USERNAME=postgres
      - DB_PASSWORD=admin
    volumes:
      - ./backend/alab:/var/www/html
      - /var/www/html/vendor

  frontend:
    build:
      context: ./frontend/frontend
      dockerfile: Dockerfile.frontend
    container_name: vue_frontend
    ports:
      - "8080:8080"
    depends_on:
      - backend
    environment:
      - VUE_APP_BACKEND_URL=http://localhost:8000
    volumes:
      - ./frontend/frontend:/app
      - /app/node_modules

  db:
    image: postgres:13
    container_name: postgres_db
    restart: always
    environment:
      - POSTGRES_DB=alab
      - POSTGRES_USER=postgres
      - POSTGRES_PASSWORD=admin
    ports:
      - "5432:5432"
    volumes:
      - pgdata:/var/lib/postgresql/data

volumes:
  pgdata:
```

4. Uruchom kontenery:

```bash
docker-compose up --build
```

5. Po uruchomieniu kontenerów, wykonaj migracje bazy danych:

```bash
docker-compose exec backend php artisan migrate
```

6. Zaimportuj dane z pliku CSV:

```bash
docker-compose exec backend php artisan app:import-patient-data storage/app/private/results.csv
```

Powinieneś zobaczyć komunikat: "Importing patient data...Data imported successfully."
7. Aplikacja frontendowa jest dostępna na porcie 8080. Użyj loginu i hasła zgodnie ze specyfikacją zadania.

## CI/CD

Przykładowy workflow CI uruchamiający testy znajduje się w pliku `.github/workflows/laravel.yml` w repozytorium backend.

Przykład poprawnie uruchomionego workflowa można znaleźć [tutaj](https://github.com/meamea21/alab/actions/runs/13446386003/job/37572570578).