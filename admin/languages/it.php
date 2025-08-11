<?php
return [
    // Navigazione globale / voci comuni (allineate a en.php)
    'dashboard' => 'Dashboard',
    'users' => 'Utenti',
    'products' => 'Prodotti',
    'orders' => 'Ordini',
    'suppliers' => 'Fornitori', // etichetta menu (diverso da array modulo 'suppliers')
    'branches' => 'Filiali',
    'reports' => 'Report',
    'settings' => 'Impostazioni',
    'logout' => 'Esci',
    'profile' => 'Profilo',
    'btn' => [
        'add_new' => 'Aggiungi Nuovo',
        'save' => 'Salva',
        'cancel' => 'Annulla',
        'delete' => 'Elimina',
        'edit' => 'Modifica',
        'update' => 'Aggiorna',
        'view' => 'Visualizza',
        'refresh' => 'Aggiorna',
        'back' => 'Indietro',
        'close' => 'Chiudi',
        'yes_delete' => 'Sì, elimina',
        'confirm' => 'Conferma'
    ],

    'msg' => [
        'created_successfully' => 'Creato con successo',
        'updated_successfully' => 'Aggiornato con successo',
        'deleted_successfully' => 'Eliminato con successo',
        'error_occurred' => 'Si è verificato un errore',
        'not_found' => 'Elemento non trovato',
        'invalid_token' => 'Token di sicurezza non valido',
        'required_field' => 'Campo obbligatorio',
        'confirm_delete' => 'Sei sicuro di voler eliminare questo elemento?',
        'confirm_delete_text' => 'Questa azione non può essere annullata.',
        'saved' => 'Salvato con successo',
        'loading' => 'Caricamento...',
        'no_data' => 'Nessun dato disponibile'
    ],

    'form' => [
        'required' => 'Obbligatorio',
        'optional' => 'Opzionale',
        'select_option' => 'Seleziona un\'opzione',
        'enter_value' => 'Inserisci un valore',
        'choose_file' => 'Scegli file'
    ],

    'common' => [
        'name' => 'Nome',
        'description' => 'Descrizione',
        'email' => 'Email',
        'phone' => 'Telefono',
        'address' => 'Indirizzo',
        'status' => 'Stato',
        'created' => 'Creato',
        'updated' => 'Aggiornato',
        'actions' => 'Azioni'
    ],

    'status' => [
        'active' => 'Attivo',
        'inactive' => 'Inattivo'
    ],

    'auth' => [
        'login' => 'Accedi',
        'logout' => 'Esci',
        'remember_me' => 'Ricordami',
        'forgot_password' => 'Password dimenticata?',
        'username' => 'Nome utente',
        'password' => 'Password'
    ],

    // Filiali (branch) - spostato dal modulo in file globale
    'branch' => [
        'management' => 'Gestione Filiali',
        'new_branch' => 'Nuova Filiale',
        'edit_branch' => 'Modifica Filiale',
        'branch_list' => 'Elenco Filiali',
        'branch_name' => 'Nome Filiale',
        'branch_code' => 'Codice Filiale',
        'manager' => 'Responsabile',
        'location' => 'Località',
        'contact_info' => 'Informazioni di Contatto',
        'total_branches' => 'Totale Filiali',
        'active_branches' => 'Filiali Attive',
        'manage_users' => 'Gestisci Utenti',
        'confirm_delete_title' => 'Conferma Eliminazione',
        'confirm_delete_message' => 'Sei sicuro di voler eliminare la filiale',
        'delete_warning' => 'Questa azione rimuoverà anche tutte le assegnazioni degli utenti.'
    ],

    // =======================
    // Modulo Fornitori
    // =======================
    'suppliers' => [
        'products_title' => 'Prodotti',
        'add_product' => 'Aggiungi Prodotto',
        'manage_product' => 'Gestione Prodotto',
        'raw_material' => 'Materia Prima',
        'generate_barcode' => 'Genera Barcode',
        'requires_expiry' => 'Richiede Data di Scadenza',
        'supplier' => 'Fornitore',
        'unit' => 'Unità',
        'quantity' => 'Quantità',
        'quantity_per_unit' => 'Quantità per Unità',
        'sub_unit_level' => 'Sotto-unità Livello :level',
        'quantity_for_unit' => 'Quantità per questa Unità',
        'active' => 'Attivo',
        'associate' => 'Associa',
        'associations' => 'Associazioni',
        'back_to_products' => 'Torna ai Prodotti',
        'add_sub_unit' => 'Aggiungi Sotto-Unità',
        'cancel_edit' => 'Annulla Modifica',
        'confirm_delete' => 'Confermi la cancellazione?',
        'delete_yes' => 'Sì, elimina',
        'record_not_found' => 'Record non trovato',
        'base_quantity_gt_zero' => 'La quantità base deve essere > 0'
    ]
];