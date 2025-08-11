<?php
return [
    'configuration' => 'Configurazione di Sistema',
    'global_settings_tab' => 'Impostazioni Globali',
    'languages_tab' => 'Lingue',
    'site_name' => 'Nome Sito',
    'site_url' => 'URL Sito',
    'logo' => 'Logo',
    'currency' => 'Valuta',
    'currencies' => 'Valute',
    'currencies_help' => 'Elenco codici valuta abilitati (ISO 4217), la prima è la predefinita',
    'timezone' => 'Fuso Orario',
    'date_format_admin' => 'Formato Data Admin',
    'date_format_public' => 'Formato Data Pubblico',
    'website_enabled' => 'Sito Abilitato',
    'add_language' => 'Aggiungi Lingua',
    'code' => 'Codice',
    'name' => 'Nome',
    'direction' => 'Direzione',
    'admin' => 'Admin',
    'public' => 'Pubblico',
    'actions' => 'Azioni',
    'delete_language' => 'Elimina Lingua',

    'modal' => [
        'direction_ltr' => 'LTR',
        'direction_rtl' => 'RTL'
    ],

    'success' => [
        'settings_updated' => 'Impostazioni aggiornate con successo',
        'language_added' => 'Lingua aggiunta con successo',
        'language_deleted' => 'Lingua eliminata con successo',
        'logo_deleted' => 'Logo eliminato con successo'
    ],

    'error' => [
        'invalid_csrf' => 'Token di sicurezza non valido',
        'settings_update_failed' => 'Aggiornamento impostazioni fallito',
        'language_add_failed' => 'Aggiunta lingua fallita',
        'language_delete_failed' => 'Eliminazione lingua fallita',
        'unauthorized' => 'Accesso non autorizzato',
        'no_logo' => 'Nessun logo da eliminare',
        'delete_failed' => 'Eliminazione file logo fallita',
        'update_failed' => 'Aggiornamento database fallito'
    ],

    // Activity Logs / System Logs
    'system' => [
        'activity_logs' => 'Log Attività'
    ],
    'log' => [
        'details' => 'Dettagli Log',
        'not_found' => 'Log non trovato'
    ],
    'restore' => [
        'confirm' => 'Ripristinare questa modifica?',
        'description' => 'Questo tenterà di ripristinare i dati precedenti.'
    ],
    'yes_restore' => 'Sì, Ripristina',
    'details' => 'Dettagli',
    'restore' => 'Ripristina',
    // Admin Menu management keys (annidati)
    'menu' => [
        'management' => 'Gestione Menu',
        'total_items' => 'Elementi Totali',
        'active_items' => 'Elementi Attivi',
        'items' => 'Voci di Menu',
    ],
    'add' => [
        'new' => 'Aggiungi Nuovo',
        'menu' => [
            'item' => 'Aggiungi Elemento Menu'
        ],
    ],
    'parent' => [
        'menu' => 'Menu Padre'
    ],
    'select' => [
        'parent' => 'Seleziona Padre'
    ],
    'sort' => [
        'order' => 'Ordine'
    ],
    'permission' => [
        'module' => 'Modulo Permessi',
        'action' => [
            '_value' => 'Azione Permesso',
            'view' => 'Visualizza',
            'create' => 'Crea',
            'update' => 'Aggiorna',
            'delete' => 'Elimina',
        ],
    ],
    'target' => 'Target',
    'css' => [
        'class' => 'Classe CSS'
    ],
    'is' => [
        'active' => 'Attivo',
        'separator' => 'Separatore'
    ],
    'example' => [
        'url' => 'es. users.php',
        'module' => 'es. users'
    ],
    'leave' => [
        'empty' => [
            'parent' => 'Lascia vuoto per elementi di livello superiore',
            'permission' => 'Lascia vuoto per nessun controllo permessi'
        ]
    ],
    'preview' => 'Anteprima',
    'browse' => [
        'icons' => 'Sfoglia Icone FontAwesome',
        'icons_info' => 'Apri il sito FontAwesome per cercare le icone'
    ],
    'same' => [
        'window' => 'Stessa Finestra'
    ],
    'new' => [
        'window' => 'Nuova Finestra'
    ],
    'icon' => [
        'placeholder' => 'fas fa-circle'
    ],
    'custom' => [
        'class' => [
            'placeholder' => 'custom-class'
        ]
    ],
    'root' => 'Radice',
    'order' => 'Ordine',
    // (Status & confirm keys removed - now global)
];