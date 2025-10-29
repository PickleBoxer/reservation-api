# Arhitekturne odločitve in tehnične rešitve

## Pregled projekta

Reservation API je minimalni RESTful API za ustvarjanje rezervacij različnih virov (sobe, vozila, prostori) v prostih časovnih oknih z zaščito pred race conditions.

## Podatkovni model

### Tabele

1. **resources** (viri)
   - Predstavlja vire, ki jih lahko rezerviramo
   - Tip vira je shranjen kot string za fleksibilnost

2. **reservations** (rezervacije)
   - Povezava do vira preko `resource_id`
   - Opcijska povezava do uporabnika (`user_id`) - pripravljena za avtentikacijo
   - Časovna obdobja: `start_time`, `end_time`
   - Indeksi optimizirani za preverjanje konfliktov

## Arhitektura

### Layered Architecture

```
┌─────────────────────────────────────┐
│  Controller (HTTP Layer)            │  ← Sprejema HTTP zahteve
├─────────────────────────────────────┤
│  Service (Business Logic)           │  ← Usmerja tok logike
├─────────────────────────────────────┤
│  Repository (Data Access)           │  ← Operacije z bazo podatkov
├─────────────────────────────────────┤
│  Model (Domain)                     │  ← Eloquent relacije
└─────────────────────────────────────┘
```

### Ključne komponente:

1. **Controllers** (`ReservationController`)
   - Obravnava HTTP zahteve
   - Delegira logiko na service layer
   - Vrača strukturirane odgovore (API Resources)

2. **Service Layer** (`ReservationService`)
   - Vsebuje vso poslovno logiko
   - Preverjanje konfliktov
   - Transakcijska obdelava

3. **Models**
   - Eloquent modeli z relacijami
   - Type casting za datetime polja

4. **Form Requests**
   - Centralizirana validacija
   - Custom error sporočila
   - Avtorizacija

5. **API Resources**
   - Konsistentna struktura odgovorov
   - Skrivanje nepotrebnih polj
   - Transformacija podatkov (npr. ISO8601 datumi)

## Race Condition Protection

**Kako deluje:**
1. Transakcija začne
2. Resource vrstica se zaklene (`FOR UPDATE`)
3. Preverijo se konflikti
4. Rezervacija se ustvari
5. Lock se sprosti

### Pokrite edge cases:

1. ✅ Popolno prekrivanje (enaki časi)
2. ✅ Nova rezervacija se začne med obstoječo
3. ✅ Nova rezervacija se konča med obstoječo
4. ✅ Nova rezervacija objame obstoječo
5. ✅ Back-to-back rezervacije (dovoljene)
6. ✅ Različni viri (dovoljeni)
7. ✅ Concurrent requests (pessimistic locking)

## Database: SQLite → PostgreSQL Ready

### Trenutno: SQLite
- ✅ Dobro za development
- ⚠️ Omejeno za vzporedne zapise

## API 

### Endpoint:
```
POST /api/reservations
```

### Request:
```json
{
  "resource_id": 1,
  "start_time": "2024-12-01T10:00:00Z",
  "end_time": "2024-12-01T12:00:00Z",
  "customer_name": "John Doe",
  "customer_email": "john@example.com",
  "notes": "Optional notes"
}
```

### Response (Success 201):
```json
{
  "message": "Reservation created successfully",
  "data": {
    "id": 1,
    "resource": {...},
    "start_time": "2024-12-01T10:00:00Z",
    "end_time": "2024-12-01T12:00:00Z",
    "status": "confirmed",
    "customer": {...}
  }
}
```
