<?php
return [
    // (Navigation & menu keys removed - now global)

    // Error messages
    'error' => [
        '404_title' => 'Pagina Non Trovata',
        '404_message' => 'La pagina che stai cercando non è stata trovata. Controlla l\'URL o torna alla dashboard.',
    ],

    'page_not_found' => 'Pagina Non Trovata',
    'back_to_dashboard' => 'Torna alla Dashboard',

    // (User management keys removed - handled by access-management module / global)

    // (Branch management keys removed - now global)

    // (Supplier management keys removed - in suppliers module)

    // (Product management keys removed - not restaurant-specific)

    // (Order management keys removed - not restaurant-specific)

    // (Generic button keys removed - now global)

    // (Generic message keys removed - now global)

    // (Generic form keys removed - now global)

    // Additional missing translations
    'password' => 'Password',
    'min_characters' => 'Caratteri minimi',
    'instructions' => 'Istruzioni',
    'important_info' => 'Informazioni Importanti',
    'security' => 'Sicurezza',
    'ensure_secure_passwords' => 'Assicurati di utilizzare password sicure e di assegnare solo i permessi necessari.',
    'fields_marked_required' => 'I campi contrassegnati con * sono obbligatori',
    'username_must_be_unique' => 'L\'username deve essere unico nel sistema',
    'password_min_length' => 'La password deve essere di almeno 6 caratteri',
    'assign_groups_after_creation' => 'Dopo la creazione, sarà possibile assegnare l\'utente ai gruppi',
    'validation_error' => 'Errore Validazione',
    'fill_required_fields' => 'Per favore, compila tutti i campi obbligatori',

    // Impostazioni specifiche
    'general_settings' => 'Impostazioni Generali',
    'site_name' => 'Nome Sito',
    'site_url' => 'URL Sito',
    'site_logo' => 'Logo Sito',
    'upload_logo' => 'Carica Logo',
    'current_logo' => 'Logo Attuale',
    'no_logo' => 'Nessun logo caricato',
    'rooms_management' => 'Gestione Sale',
    'tables_management' => 'Gestione Tavoli',
    'room' => 'Sala',
    'rooms' => 'Sale',
    'table' => 'Tavolo',
    'tables' => 'Tavoli',
    'add_room' => 'Aggiungi Sala',
    'edit_room' => 'Modifica Sala',
    'add_table' => 'Aggiungi Tavolo',
    'edit_table' => 'Modifica Tavolo',
    'room_name' => 'Nome Sala',
    'room_description' => 'Descrizione Sala',
    'table_name' => 'Nome Tavolo',
    'seats' => 'Posti',
    'select_branch' => 'Seleziona Filiale',
    'select_room' => 'Seleziona Sala',
    'room_examples' => 'Esempi: Sala Principale, Terrazza, Balcone, Sala Privata, Giardino, ecc.',
    'table_examples' => 'Esempi: Tavolo 1, A1, Terrazza-1, ecc.',

    // Messaggi per nuove funzionalità (annidati)
    'msg' => [
        'settings_updated_successfully' => 'Impostazioni aggiornate con successo',
        'room_created_successfully' => 'Sala creata con successo',
        'room_updated_successfully' => 'Sala aggiornata con successo',
        'room_deleted_successfully' => 'Sala eliminata con successo',
        'table_created_successfully' => 'Tavolo creato con successo',
        'table_updated_successfully' => 'Tavolo aggiornato con successo',
        'table_deleted_successfully' => 'Tavolo eliminato con successo',
        'cannot_delete_room_has_tables' => 'Non è possibile eliminare una sala che contiene tavoli',
        'required_fields' => 'Campi obbligatori mancanti',
        'invalid_token' => 'Token di sicurezza non valido',
        'not_found' => 'Elemento non trovato',
        'created_successfully' => 'Creato con successo',
        'updated_successfully' => 'Aggiornato con successo',
        'deleted_successfully' => 'Eliminato con successo',
        'error_occurred' => 'Si è verificato un errore',
    ],

    // Gestione Zone di Consegna
    'add_delivery_area' => 'Aggiungi Zona di Consegna',
    'edit_delivery_area' => 'Modifica Zona di Consegna',
    'area_name' => 'Nome Zona',
    'area_name_required' => 'Il nome della zona è obbligatorio',
    'branch_required' => 'La selezione della filiale è obbligatoria',
    'no_branch' => 'Nessuna Filiale',
    'delivery_area_created' => 'Zona di consegna creata con successo',
    'delivery_area_updated' => 'Zona di consegna aggiornata con successo',
    'delivery_area_deleted' => 'Zona di consegna eliminata con successo',
    'delivery_area_not_found' => 'Zona di consegna non trovata',
    'error_creating_delivery_area' => 'Errore nella creazione della zona di consegna',
    'error_updating_delivery_area' => 'Errore nell\'aggiornamento della zona di consegna',
    'error_deleting_delivery_area' => 'Errore nell\'eliminazione della zona di consegna',
    'delete_delivery_area_confirm' => 'Sei sicuro di voler eliminare la zona di consegna "%s"?',
    'branch' => 'Filiale',
    'created_at' => 'Creato il',
    'id' => 'ID',
    'back' => 'Indietro',
    'invalid_request' => 'Richiesta non valida',
    'no_permission' => 'Non hai i permessi per accedere a questa pagina',
    'confirm_delete' => 'Conferma Eliminazione',
    'yes_delete' => 'Sì, Elimina',

    // Modulo Ricette (annidato)
    'restaurant' => [
        'menu' => 'Ristorante',
        'recipes' => 'Ricette',
        'production' => 'Produzione',
    ],
    'recipes' => [
        'title' => 'Ricette',
        'action' => [
            'new' => 'Nuova',
            'edit' => 'Modifica',
            'delete' => 'Elimina',
            'production' => 'Produzione',
            'add_component' => 'Aggiungi Componente',
        ],
        'field' => [
            'name' => 'Nome',
            'yield' => 'Resa',
            'components' => 'Componenti',
            'actions' => 'Azioni',
            'output_quantity' => 'Quantità Output',
            'reference_code' => 'Codice Riferimento',
            'base_qty' => 'Qtà Base',
            'scaled_qty' => 'Qtà Scalata',
            'unit' => 'Unità',
            'yield_label' => 'Resa',
        ],
        'production' => [
            'batch_title' => 'Batch Produzione',
        ],
        'msg' => [
            'batch_success' => 'Batch prodotto con successo',
            'batch_error' => 'Errore produzione batch',
        ],
        'components' => [
            'preview' => 'Anteprima Componenti',
        ],
        'confirm' => [
            'delete' => 'Eliminare la ricetta?',
        ],
    ],

    // Inventario (annidato)
    'inventory' => [
        'title' => 'Inventario',
        'field' => [
            'type' => 'Tipo',
            'updated_at' => 'Aggiornato',
            'quantity' => 'Quantità',
            'unit' => 'Unità',
        ],
        'reason' => [
            'batch_consume' => 'Consumo Batch',
            'batch_produce' => 'Produzione Batch',
            'po_receive' => 'Ricezione Ordine Acquisto',
        ],
    ],
    'cancel' => 'Annulla',

];
