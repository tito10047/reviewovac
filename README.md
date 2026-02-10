### Príkazy

#### Spracovanie nových recenzií
Príkaz načíta všetky nespracované recenzie z databázy a pre každú z nich odošle správu do Symfony Messenger na ďalšie spracovanie.

```bash
php bin/console app:process-new-reviews
```

> [!IMPORTANT]
> **Spracovanie správ (Messenger):**
> - V **vývojovom prostredí (dev)** sú správy spracovávané **synchrónne** pre jednoduchší vývoj a ladenie.
> - V **produkčnom prostredí** sú správy spracovávané **asynchrónne** prostredníctvom nakonfigurovaného transportu.
>
> Táto konfigurácia sa nachádza v súbore `config/packages/messenger.yaml`.

### Vývojárske nástroje

V projekte sú nakonfigurované nástroje pre udržiavanie kvality kódu. Môžeš ich spúšťať pomocou Composer skriptov:

#### Statická analýza (PHPStan)
```bash
composer phpstan
```

#### Kontrola a oprava kódového štýlu (PHP CS Fixer)
```bash
# Iba kontrola
composer cs-check

# Automatická oprava
composer cs-fix
```

### Testovanie

V projekte sú testy rozdelené do dvoch hlavných skupín: **Unit testy** a **Integračné testy**.

#### Spustenie testov

Pre spustenie testov môžeš použiť nasledujúce skratky:

```bash
# Spustenie všetkých testov
composer tests

# Iba unit testy
composer tests-unit

# Iba integračné testy
composer tests-integration

# Integračné testy s reálnymi volaniami na AI (vyžaduje nastavené API kľúče)
composer tests-real-ai
```

Pôvodné príkazy cez `phpunit` stále fungujú:
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
OPENAI_API_KEY=sk-proj-ddH....

```
