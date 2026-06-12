# Instrukcja uruchomienia

## Wymagane technologie

- PHP 8.3+
- Composer
- MariaDB
- Node.js 20.19+ (lub 22+)
- npm

## Konfiguracja

1. Utwórz bazę danych w MariaDB.
2. W katalogu `backend/` skopiuj `.env.example` do `.env` i ustaw dane połączenia z bazą (`DB_*`).
3. W katalogu `frontend/` skopiuj `.env.example` do `.env`.

## Uruchomienie

**Backend:**

```bash
cd backend
composer install
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

**Frontend** (osobny terminal):

```bash
cd frontend
npm install
npm run dev
```

Aplikacja: http://localhost:5173  
API: http://localhost:8000/api

## Konta testowe

Hasło dla wszystkich kont: `password`

- `admin@foto-rental.test` — administrator
- `user@foto-rental.test` — użytkownik
- `anna@foto-rental.test` — użytkownik
