### Testovanie

V projekte sú testy rozdelené do dvoch hlavných skupín: **Unit testy** a **Integračné testy**.

#### Spustenie testov

Pre spustenie všetkých testov použi príkaz:
```bash
vendor/bin/phpunit
```

Pre spustenie konkrétnej sady testov:
```bash
# Iba unit testy
vendor/bin/phpunit --testsuite Unit

# Iba integračné testy
vendor/bin/phpunit --testsuite Integration
```

#### Integračné testy a REAL_AI

Integračné testy, ktoré využívajú reálne volania na AI služby (napr. `ReviewProcessServiceTest`), sú v predvolenom nastavení **preskakované**, aby sa zbytočne nemíňali kredity pri bežnom vývoji.

Ak chceš spustiť aj tieto testy, musíš nastaviť premennú prostredia `REAL_AI=1`:
```bash
REAL_AI=1 vendor/bin/phpunit --testsuite Integration
```

### Konfigurácia testovacieho prostredia

Pre lokálne spustenie integračných testov a správne fungovanie testovacieho prostredia je potrebné vytvoriť súbor `.env.test.local` (ak neexistuje) a vyplniť v ňom potrebné údaje.

Súbor by mal obsahovať aspoň tieto premenné:

```ini
# Pripojenie k databáze pre testy
DATABASE_URL="mysql://uzivatel:heslo@127.0.0.1:3306/názov_databázy"

# API kľúče pre AI služby (potrebné pre integračné testy s REAL_AI=1)
OPENAI_API_KEY=sk-proj-ddH
OPEN_ORGANISATION_ID=org-w4
OPEN_PROJECT_ID=proj_
```
