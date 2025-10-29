# Reservation API - Laravel 12

RESTful API za upravljanje rezervacij različnih virov (sobe, vozila, prostori).

## 📋 Zahteve

- **PHP 8.3+**
- **Composer**
- **SQLite** (development) / **PostgreSQL** (production)

## 🚀 Namestitev

### Možnost 1: Korak za korakom

```bash
# Kloniraj repozitorij
git clone https://github.com/PickleBoxer/reservation-api.git
cd reservation-api

# Namesti odvisnosti
composer install

# Kopiraj .env datoteko
cp .env.example .env

# Generiraj aplikacijski ključ
php artisan key:generate

# Ustvari SQLite bazo
touch database/database.sqlite

# Poženi migracijo in seeder
php artisan migrate --seed

# Zaženi razvojni strežnik
php artisan serve
```

### Možnost 2: Hitro nastavljanje

```bash
# Kloniraj repozitorij
git clone <repository-url>
cd reservation-api

# All-in-one ukaz
composer run setup

# Zaženi razvojni strežnik
php artisan serve
```

> [!NOTE]
> V terminalu se izpiše personal token za dostop do API-ja.

API bo na voljo na: `http://localhost:8000`

## 🔗 API Endpoints

| Metoda | Endpoint | Opis |
|--------|----------|------|
| `POST` | `/api/reservations` | Ustvari novo rezervacijo |

## 💡 Primeri uporabe

### Ustvari novo rezervacijo

```bash
curl -X POST http://localhost:8000/api/reservations \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer <YOUR_API_TOKEN>" \
  -H "Accept: application/json" \
  -d '{
    "resource_id": 1,
    "start_time": "2024-12-01T10:00:00Z",
    "end_time": "2024-12-01T12:00:00Z",
    "customer_name": "John Doe",
    "customer_email": "john@example.com",
    "notes": "Important meeting"
  }'
```

**Pričakovan odziv (201):**

```json
{
  "message": "Reservation created successfully",
  "data": {
    "id": 1,
    "resource": {
      "id": 1,
      "name": "Conference Room A",
      "type": "room"
    },
    "start_time": "2025-12-01T10:00:00Z",
    "end_time": "2025-12-01T12:00:00Z",
    "customer": {
      "name": "John Doe",
      "email": "john@example.com"
    },
    "notes": "Important meeting"
  }
}
```

## 📚 API Dokumentacija

### `POST /api/reservations`

**Headers:**

| Header | Vrednost | Obvezno |
|--------|----------|---------|
| `Content-Type` | `application/json` | ✅ |
| `Accept` | `application/json` | ✅ |
| `Authorization` | `Bearer <YOUR_API_TOKEN>` | ✅ |

**Obvezna polja:**

| Polje | Tip | Opis |
|-------|-----|------|
| `resource_id` | `integer` | ID vira za rezervacijo |
| `start_time` | `string` | Začetek rezervacije (ISO8601 format) |
| `end_time` | `string` | Konec rezervacije (ISO8601 format, mora biti po start_time) |
| `customer_name` | `string` | Ime stranke (max 255 znakov) |
| `customer_email` | `string` | Email stranke (veljaven email) |

**Opcijska polja:**

| Polje | Tip | Opis |
|-------|-----|------|
| `notes` | `string` | Opombe (max 1000 znakov) |

### Odgovori

<details>
<summary><strong>✅ Uspešen odgovor (201)</strong></summary>

```json
{
  "message": "Reservation created successfully",
  "data": {
    // ReservationResource object
  }
}
```

</details>

<details>
<summary><strong>❌ Možne napake</strong></summary>

| Status | Opis |
|--------|------|
| `409 Conflict` | Časovni termin ni na voljo |
| `422 Validation Error` | Neveljavni vhodni podatki |
| `404 Not Found` | Vir ne obstaja |
| `401 Unauthorized` | Neveljaven API token |

</details>

## 🧪 Testiranje

```bash
# Poženi vse teste
php artisan test

# Poženi teste z verbose outputom
php artisan test --verbose

# Poženi specifične teste
php artisan test --filter ReservationTest
```

## 🔧 Utility ukazi

### Resetiranje baze

```bash
# Izprazni in ponovno ustvari bazo
php artisan migrate:fresh --seed
```

### Čiščenje cache-a

```bash
# Počisti cache
php artisan config:clear && php artisan cache:clear
```

## 🐛 Težave in rešitve

<details>
<summary><strong>Baza se ne ustvari</strong></summary>

```bash
# Preveri, da je SQLite baza ustvarjena
touch database/database.sqlite

# Preveri dovoljenja
chmod 664 database/database.sqlite
```

</details>

<details>
<summary><strong>Napake pri konfiguraciji</strong></summary>

```bash
# Preveri .env datoteko
cat .env | grep DB_CONNECTION
# Naj bo: DB_CONNECTION=sqlite

# Počisti cache
php artisan config:clear && php artisan cache:clear
```

</details>

<details>
<summary><strong>API ne deluje</strong></summary>

- Preveri, da je strežnik zagnan: `php artisan serve`
- Preveri API token v terminalu po `migrate --seed`
- Preveri, da uporabljaš pravilne headerje v zahtevah

</details>
