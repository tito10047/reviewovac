# Reviewovac

Tento projekt slúži na automatizované spracovanie a analýzu produktových recenzií pomocou umelej inteligencie (AI).

## Hlavné funkcie

- **Analýza sentimentu:** AI automaticky vyhodnocuje sentiment recenzie (pozitívny, neutrálny, negatívny).
- **Detekcia problémov:** Identifikácia, či sa recenzia týka konkrétneho problému s produktom.
- **Automatický preklad:** Preklad textu recenzií do všetkých podporovaných jazykov definovaných v systéme.
- **Asynchrónne spracovanie:** Využitie Symfony Messenger pre efektívne spracovanie veľkého množstva recenzií na pozadí.

## Architektúra

Projekt je postavený na Symfony 8 a využíva moderné bundle:
- **Symfony AI Bundle:** Pre komunikáciu s AI modelmi (napr. OpenAI).
- **SymfonyCasts ObjectTranslationBundle:** Pre ukladanie prekladov entít priamo v databáze.
- **Symfony Messenger:** Pre asynchrónnu komunikáciu medzi komponentmi.
- **Bug catcher:** Pre logovanie chýb do externého dashboardu. Viac informácií nájdete na [php-bug-catcher/bug-catcher](https://github.com/php-bug-catcher/bug-catcher). Pre prístup k systému kontaktujte autora repozitára.

### Kľúčové triedy
- `Review`: Hlavná entita reprezentujúca produktovú recenziu.
- `ReviewProcessService`: Služba, ktorá zastrešuje komunikáciu s AI a získavanie analýz.
- `ProcessReviewHandler`: Message handler, ktorý koordinuje spracovanie jednej recenzie.
- `TranslationManager`: Nástroj na perzistenciu prekladov do databázy.

## Inštalácia a spustenie

Pre úspešné rozbehnutie projektu postupujte podľa nasledujúcich krokov:

1. **Klonovanie repozitára:**
   ```bash
   git clone git@github.com:tito10047/reviewovac.git
   cd reviewovac
   ```

2. **Konfigurácia prostredia:**
   Vytvorte si lokálny konfiguračný súbor a nastavte v ňom potrebné premenné (napr. prístup k databáze a API kľúče).
   ```bash
   cp .env .env.local
   ```

3. **Spustenie migrácií:**
   Pripravte si databázovú schému:
   ```bash
   php bin/console doctrine:migrations:migrate --no-interaction
   ```

4. **Nahranie testovacích dát (voliteľné):**
   Ak chcete začať s testovacími dátami, spustite fixtures:
   ```bash
   php bin/console doctrine:fixtures:load --no-interaction
   ```

5. **Spracovanie nových recenzií:**
   Pre spustenie procesu spracovania nových recenzií použite príkaz:
   ```bash
   php bin/console app:process-new-reviews
   ```

## Príkazy

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

### Rozšíriteľnosť

V prípade potreby rozšírenia systému o nové možnosti postupujte nasledovne:

- **Pridanie nového jazyka:** Nový jazyk musí byť pridaný do enumu `App\Enum\Language`.
- **Pridanie nového sentimentu:** Ak je potrebné rozlíšiť iný typ sentimentu, pridajte ho do enumu `App\Enum\ReviewSentiment`.
- **Konfigurácia AI promptu:** Texty promptov a modely pre AI analýzu sa konfigurujú v súbore `config/packages/ai.yaml`.

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
