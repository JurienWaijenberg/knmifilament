# Deployment Instructies voor TransIP

## Na de eerste deployment

Na het uploaden van de bestanden via GitHub Actions, moet je de volgende stappen uitvoeren op de server:

### 1. Document Root instellen

Zorg ervoor dat de **Document Root** in je TransIP controlepaneel verwijst naar de `public` map:
- Ga naar je webhostingpakket in het controlepaneel
- Stel de Document Root in naar: `/www/` (of de map waar je Laravel app staat) + `/public`
- Bijvoorbeeld: Als je app in `/www/knmifilament/` staat, dan moet Document Root zijn: `/www/knmifilament/public`

### 2. .env bestand aanmaken

1. Log in via FTP/SFTP
2. Upload of maak een `.env` bestand aan in de root van je Laravel applicatie (niet in de public map!)
3. Kopieer de inhoud van `.env.example` en pas aan:
   ```env
   APP_NAME="KNMI Filament"
   APP_ENV=production
   APP_KEY=base64:JE_MOET_HIER_EEN_KEY_GENEREREN
   APP_DEBUG=false
   APP_URL=https://jouw-domein.nl
   
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=je_database_naam
   DB_USERNAME=je_database_user
   DB_PASSWORD=je_database_wachtwoord
   ```

### 3. APP_KEY genereren

Log in via SSH (als beschikbaar) of gebruik een FTP client met terminal toegang:
```bash
php artisan key:generate
```

Of genereer een key lokaal en zet deze in je `.env` bestand.

### 4. Bestandspermissies instellen

Zorg ervoor dat de volgende mappen schrijfrechten hebben (755 of 775):
- `storage/` (en alle submappen)
- `bootstrap/cache/`

Je kunt dit doen via:
- **File Manager** in TransIP controlepaneel: Rechtsklik op de map → Eigenschappen → Rechten instellen op 755 of 775
- **FTP Client**: Rechtsklik op map → File Permissions → 755 of 775

### 5. Database migraties uitvoeren

Als je SSH toegang hebt:
```bash
php artisan migrate --force
```

Anders kun je dit lokaal doen en de database direct op de server aanpassen, of gebruik een database management tool zoals phpMyAdmin.

### 6. Storage link aanmaken (optioneel)

Als je publieke bestanden wilt serveren:
```bash
php artisan storage:link
```

### 7. Cache optimaliseren

Voor betere performance:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Troubleshooting

### "500 Internal Server Error"
- Controleer de error logs in TransIP controlepaneel
- Zorg dat `.env` bestand bestaat en correct is
- Controleer bestandspermissies op `storage/` en `bootstrap/cache/`

### "APP_KEY not set"
- Genereer een nieuwe key: `php artisan key:generate`
- Of zet handmatig een key in `.env`: `APP_KEY=base64:...`

### "Permission denied" errors
- Zet permissies op `storage/` en `bootstrap/cache/` naar 755 of 775
- Controleer of de webserver gebruiker (meestal `www-data` of `apache`) rechten heeft

### Database connectie problemen
- Controleer database credentials in `.env`
- Zorg dat de database bestaat in TransIP controlepaneel
- Controleer of `DB_HOST` correct is (meestal `127.0.0.1` of `localhost`)

## Belangrijke bestanden die NIET geüpload worden

De volgende bestanden worden automatisch uitgesloten tijdens deployment:
- `.env` (moet handmatig op de server worden aangemaakt)
- `storage/logs/*` (wordt lokaal op de server aangemaakt)
- `storage/framework/cache/*` (wordt lokaal op de server aangemaakt)
- `node_modules/` (niet nodig op de server)
- `.git/` (niet nodig op de server)

## Automatische deployment

Bij elke push naar de `main` of `master` branch wordt automatisch een nieuwe deployment gestart via GitHub Actions.
