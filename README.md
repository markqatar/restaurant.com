# Sistema di Gestione Ristorante

Un sistema completo di gestione ristorante sviluppato in PHP e MySQL con tutte le funzionalità richieste per amministrare un ristorante moderno.

## Caratteristiche Principali

### Pannello Admin
- **Gestione Utenti**: CRUD completo per utenti, gruppi e permessi granulari
- **Gestione Filiali**: Configurazione sedi con opzioni delivery, pickup, dine-in
- **Gestione Fornitori**: Ordini, materiali prime, inventario con codici a barre
- **Gestione Ricette**: Ricette da materie prime o altre ricette
- **Produzione in Lotti**: Generazione prodotti finali con tracciabilità
- **Gestione Prodotti**: Categorie e prodotti finali con supporto multilingua
- **Gestione Clienti**: Clienti, indirizzi, gruppi sconto
- **Marketing**: Coupon e promozioni avanzate
- **Gestione Ordini**: Sistema completo ordini con stampa su stampanti termiche
- **Report**: Sistema di reporting dettagliato
- **CMS**: Blog e pagine statiche
- **Impostazioni**: Configurazione completa del sistema

### Sito Pubblico
- **Registrazione/Login**: Email, Google, Apple, Facebook
- **Dashboard Cliente**: Gestione profilo e storico ordini
- **Ordinazione Online**: Carrello e checkout completo
- **Pagamenti**: My Fatoorah, Contanti, Carta alla consegna
- **Multilingua**: Italiano, Inglese, Arabo (RTL)
- **Design Responsivo**: Ottimizzato per tutti i dispositivi

## Tecnologie Utilizzate

- **Backend**: PHP 8.0+
- **Database**: MySQL 8.0+
- **Frontend**: Bootstrap 5, JavaScript ES6
- **UI Components**: Font Awesome, DataTables, Chart.js, SweetAlert2
- **Sicurezza**: CSRF Protection, Password Hashing, SQL Injection Prevention

## Installazione

### Requisiti di Sistema
- PHP 8.0 o superiore
- MySQL 8.0 o superiore
- Apache/Nginx
- Estensioni PHP: PDO, mbstring, fileinfo, gd

### Passaggi di Installazione

1. **Clona o scarica il progetto**
   ```bash
   git clone [repository-url]
   cd restaurant-management
   ```

2. **Configura il database**
   ```bash
   mysql -u root -p
   ```
   ```sql
   CREATE DATABASE restaurant_management;
   exit
   ```

3. **Importa lo schema del database**
   ```bash
   mysql -u root -p restaurant_management < install/database.sql
   ```

4. **Configura la connessione al database**
   Modifica il file `config/database.php` con le tue credenziali:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'restaurant_management');
   ```

5. **Configura i permessi delle cartelle**
   ```bash
   chmod 755 uploads/
   chmod 755 assets/
   ```

6. **Configura il web server**
   - Punta il document root a `/restaurant-management/`
   - Abilita mod_rewrite per Apache
   - Configura SSL per produzione

## Configurazione

### Credenziali Default Admin
- **Username**: admin
- **Email**: admin@restaurant.com
- **Password**: admin123

⚠️ **IMPORTANTE**: Cambia immediatamente la password di default dopo l'installazione!

### Configurazione Stampanti
1. Vai su **Admin > Impostazioni > Stampanti**
2. Aggiungi le stampanti termiche con IP e porta
3. Associa le stampanti alle categorie di prodotti

### Configurazione Pagamenti
1. **My Fatoorah**: Configura API key nelle impostazioni
2. **Altri metodi**: Attiva/disattiva dalla configurazione pagamenti

### Configurazione Email
Configura SMTP nelle impostazioni per notifiche automatiche:
- Conferme ordini
- Recupero password
- Notifiche scadenze

## Utilizzo

### Pannello Admin
1. Accedi a `/admin/` con le credenziali
2. Configura le filiali e i prodotti
3. Imposta i permessi utente
4. Configura stampanti e pagamenti

### Sito Pubblico
1. I clienti possono registrarsi e ordinare
2. Il carrello è salvato localmente
3. Gli ordini sono processati in tempo reale

## Funzionalità Avanzate

### Codici a Barre
- Generazione automatica per materie prime
- Scansione durante la produzione
- Tracciabilità completa

### Notifiche Scadenze  
- Cron job per controllo scadenze
- Email automatiche per prodotti in scadenza
- Dashboard con alert in tempo reale

### Stampa Termica
- Stampa automatica ordini su stampanti 80mm
- Raggruppamento per categoria
- Supporto Inglese/Arabo per prodotti

### Permessi Granulari
- Controllo per ogni modulo
- Azioni specifiche (view, create, update, delete)
- Gruppi utente personalizzabili

### Multilingua Completo
- Interfaccia admin multilingua
- Contenuti tradotti dinamicamente
- Supporto RTL per Arabo

## Manutenzione

### Backup Database
```bash
mysqldump -u username -p restaurant_management > backup.sql
```

### Pulizia File Upload
- Pulisci periodicamente la cartella uploads/
- Verifica file orfani senza referenze

### Monitoraggio
- Controlla log degli errori PHP
- Monitora performance delle query
- Verifica spazio disco

## Sicurezza

- Validazione input su tutti i form
- Protezione CSRF su azioni sensibili
- Hash sicuro delle password
- Sanitizzazione output per XSS
- Permessi file appropriati

## Support e Personalizzazioni

Per supporto tecnico o personalizzazioni, contatta il team di sviluppo.

### Possibili Estensioni
- App mobile per clienti
- Integrazione POS
- Analytics avanzati
- Sistema di recensioni
- Programma fedeltà
- Integrazione social media

## Licenza

Questo software è proprietario. Tutti i diritti riservati.

---

**Versione**: 1.0.0  
**Data Release**: 2024  
**Autore**: Team di Sviluppo Restaurant Management