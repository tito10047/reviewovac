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

## Inštalácia a spustenie (Docker)

Pre úspešné rozbehnutie projektu pomocou Dockeru postupujte podľa nasledujúcich krokov:

1. **Klonovanie repozitára:**
   ```bash
   git clone git@github.com:tito10047/reviewovac.git
   cd reviewovac
   ```

2. **Štart prostredia:**
   Všetky potrebné služby (PHP, PostgreSQL, Caddy) sa spustia jedným príkazom:
   ```bash
   docker compose up -d --wait
   ```

3. **Spustenie migrácií:**
   Pripravte si databázovú schému v kontajneri:
   ```bash
   docker compose exec php bin/console doctrine:migrations:migrate --no-interaction
   ```

4. **Nahranie testovacích dát (voliteľné):**
   Ak chcete začať s testovacími dátami, spustite fixtures:
   ```bash
   docker compose exec php bin/console doctrine:fixtures:load --no-interaction
   ```

5. **Spracovanie nových recenzií:**
   Pre spustenie procesu spracovania nových recenzií použite príkaz:
   ```bash
   docker compose exec php bin/console app:process-new-reviews
   ```

## Príkazy

#### Spracovanie nových recenzií
Príkaz načíta všetky nespracované recenzie z databázy a pre každú z nich odošle správu do Symfony Messenger na ďalšie spracovanie.

```bash
docker compose exec php bin/console app:process-new-reviews
```

#### Nahranie testovacích dát (Fixtures)
Ak chcete vymazať aktuálnu databázu a nahrať do nej čerstvé testovacie dáta:

```bash
docker compose exec php bin/console doctrine:fixtures:load --no-interaction
```

> [!WARNING]
> Príkaz `doctrine:fixtures:load` predvolene **vymaže všetky existujúce dáta** v databáze pred nahraním nových!

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

### Budúce vylepšenia

V pláne sú nasledujúce vylepšenia systému:

- **Command na aktualizáciu chýbajúcich prekladov:** Ak je pridaný nový jazyk do systému, treba spustiť command na doplnenie chýbajúcich prekladov pre existujúce recenzie.
- **Kontrola AI výstupu:** Implementácia kontroly, či AI vrátilo všetky požadované jazyky v odpovedi.

### Vývojárske nástroje

Všetky nástroje spúšťajte cez Docker kontajner:

#### Statická analýza (PHPStan)
```bash
docker compose exec php composer phpstan -- --memory-limit=512M
```

#### Kontrola a oprava kódového štýlu (PHP CS Fixer)
```bash
# Iba kontrola
docker compose exec php composer cs-check

# Automatická oprava
docker compose exec php composer cs-fix
```

### Testovanie

Testy v Docker prostredí využívajú samostatnú databázu `app_test`, ktorá je automaticky nakonfigurovaná.

#### Spustenie testov

Pre spustenie testov v kontajneri použite nasledujúce príkazy:

```bash
# Spustenie všetkých testov
docker compose exec php composer tests

# Iba unit testy
docker compose exec php composer tests-unit

# Iba integračné testy
docker compose exec php composer tests-integration

# Integračné testy s reálnymi volaniami na AI (vyžaduje nastavené API kľúče v .env.local)
docker compose exec php composer tests-real-ai
```

Pôvodné príkazy cez `phpunit` v kontajneri stále fungujú:
```bash
docker compose exec php bin/phpunit
```

#### Integračné testy a REAL_AI

Integračné testy, ktoré využívajú reálne volania na AI služby, sú v predvolenom nastavení **preskakované**. Pre ich spustenie použite:

```bash
docker compose exec -e REAL_AI=1 php bin/phpunit --testsuite Integration
```

## Konfigurácia tajných kľúčov (Symfony Secrets)

Projekt využíva [Symfony Secrets](https://symfony.com/doc/current/configuration/secrets.html) pre bezpečnú správu citlivých údajov (napr. `OPENAI_API_KEY`).

### Nastavenie vo vývojovom prostredí:

1. **Generovanie kľúčov:**
   ```bash
   docker compose exec php bin/console secrets:generate-keys
   ```

2. **Nastavenie API kľúča:**
   ```bash
   echo "VÁŠ_OPENAI_API_KEY" | docker compose exec -i php bin/console secrets:set OPENAI_API_KEY -
   ```

3. **Zobrazenie nastavených kľúčov:**
   ```bash
   docker compose exec php bin/console secrets:list --reveal
   ```

### Produkčné prostredie:
V produkcii nikdy neukladajte súkromný kľúč (`config/secrets/prod/prod.decrypt.private.php`) do repozitára. Tento kľúč musí byť bezpečne doručený na server.

#### Nasadenie kľúča (SYMFONY_DECRYPTION_SECRET):
Ak ste od vývojára dostali Base64 zakódovaný dešifrovací kľúč, môžete ho v produkcii použiť nasledovne:

1. **Cez environment premennú (odporúčané):**
   Nastavte premennú `SYMFONY_DECRYPTION_SECRET` vo vašom prostredí (napr. v `.env.local` na serveri alebo v konštelácii hostingu):
   ```env
   SYMFONY_DECRYPTION_SECRET=TU_VLOZTE_BASE64_KLUC
   ```

2. **Cez súbor:**
   Uložte kľúč priamo do súboru `config/secrets/prod/prod.decrypt.private.php`. Symfony očakáva, že súbor bude vracať dešifrovaný kľúč. Ak máte Base64 reťazec, môžete ho prekonvertovať a uložiť pomocou PHP:
   ```bash
   docker compose exec php php -r 'file_put_contents("config/secrets/prod/prod.decrypt.private.php", "<?php return \"".base64_decode("TU_VLOZTE_BASE64_KLUC")."\";");'
   ```

### Prístup k databáze (SQL)
Ak potrebujete priamy prístup k databáze cez psql:
```bash
docker compose exec database psql -U app -d app
```
