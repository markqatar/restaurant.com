<?php
return [

    // Navigation & Menu
    'dashboard' => 'Dashboard',
    'users' => 'Utenti',
    'products' => 'Prodotti',
    'orders' => 'Ordini',
    'suppliers' => 'Fornitori',
    'branches' => 'Filiali',
    'reports' => 'Rapporti',
    'settings' => 'Impostazioni',
    'logout' => 'Esci',
    'profile' => 'Profilo',

    // Error messages
    'error' => [
        '404_title' => 'Pagina Non Trovata',
        '404_message' => 'La pagina che stai cercando non è stata trovata. Controlla l\'URL o torna alla dashboard.',
    ],

    'page_not_found' => 'Pagina Non Trovata',
    'back_to_dashboard' => 'Torna alla Dashboard',

    // User Management
    'user' => [
        'management' => 'Gestione Utenti',
        'new_user' => 'Nuovo Utente',
        'edit_user' => 'Modifica Utente',
        'user_list' => 'Lista Utenti',
        'total_users' => 'Totale Utenti',
        'active_users' => 'Utenti Attivi',
        'username' => 'Username',
        'first_name' => 'Nome',
        'last_name' => 'Cognome',
        'full_name' => 'Nome Completo',
        'groups' => 'Gruppi Utenti',
        'permissions' => 'Permessi',
        'no_groups' => 'Nessun Gruppo',
        'user_data' => 'Dati Utente',
        'account_info' => 'Informazioni Account',
        'user_id' => 'ID Utente',
        'created_on' => 'Creato il',
        'last_modified' => 'Ultima modifica',
        'quick_actions' => 'Azioni Rapide',
        'reset_password' => 'Reset Password',
        'delete_user' => 'Elimina Utente',
        'user_active' => 'Utente Attivo',
        'save_changes' => 'Salva Modifiche',
        'password_note' => 'Per motivi di sicurezza, la password non può essere modificata da questa sezione. Utilizzare la funzione "Reset Password" se necessario.',
        'confirm_delete' => 'Sei sicuro di voler eliminare questo utente? Questa azione non può essere annullata.',
        'confirm_reset_password' => 'Sei sicuro di voler resettare la password di questo utente? Verrà inviata una nuova password temporanea.',
    ],

    // Branch Management
    'branch' => [
        'management' => 'Gestione Filiali',
        'new_branch' => 'Nuova Filiale',
        'edit_branch' => 'Modifica Filiale',
        'branch_list' => 'Lista Filiali',
        'branch_name' => 'Nome Filiale',
        'branch_code' => 'Codice Filiale',
        'manager' => 'Responsabile',
        'location' => 'Ubicazione',
        'contact_info' => 'Informazioni di Contatto',
        'total_branches' => 'Totale Filiali',
        'active_branches' => 'Filiali Attive',
        'manage_users' => 'Gestisci Utenti',
        'confirm_delete_title' => 'Conferma Eliminazione',
        'confirm_delete_message' => 'Sei sicuro di voler eliminare la filiale',
        'delete_warning' => 'Questa azione rimuoverà anche tutte le assegnazioni utenti.',
    ],

    // Supplier Management
    'supplier' => [
        'management' => 'Gestione Fornitori',
        'new_supplier' => 'Nuovo Fornitore',
        'edit_supplier' => 'Modifica Fornitore',
        'supplier_list' => 'Lista Fornitori',
        'supplier_name' => 'Nome Fornitore',
        'company' => 'Azienda',
        'contact_person' => 'Persona di Contatto',
        'total_suppliers' => 'Totale Fornitori',
        'active_suppliers' => 'Fornitori Attivi',
        'city' => 'Città',
        'country' => 'Paese',
        'confirm_delete' => 'Sei sicuro di voler eliminare questo fornitore? Questa azione eliminerà anche tutti i contatti associati e non può essere annullata.',
    ],

    // Product Management
    'product' => [
        'management' => 'Gestione Prodotti',
        'new_product' => 'Nuovo Prodotto',
        'edit_product' => 'Modifica Prodotto',
        'product_list' => 'Lista Prodotti',
        'product_name' => 'Nome Prodotto',
        'category' => 'Categoria',
        'price' => 'Prezzo',
        'stock' => 'Scorta',
        'barcode' => 'Codice a Barre',
    ],

    // Order Management
    'order' => [
        'management' => 'Gestione Ordini',
        'new_order' => 'Nuovo Ordine',
        'edit_order' => 'Modifica Ordine',
        'order_list' => 'Lista Ordini',
        'order_number' => 'Numero Ordine',
        'customer' => 'Cliente',
        'total' => 'Totale',
        'order_date' => 'Data Ordine',
        'delivery_date' => 'Data Consegna',
    ],

    // Buttons & Actions
    'btn' => [
        'refresh' => 'Aggiorna',
        'add_new' => 'Aggiungi Nuovo',
        'view' => 'Visualizza',
        'modify' => 'Modifica',
        'remove' => 'Rimuovi',
        'confirm' => 'Conferma',
        'close' => 'Chiudi',
    ],

    // Messages
    'msg' => [
        'success' => 'Operazione completata con successo',
        'error' => 'Si è verificato un errore',
        'warning' => 'Attenzione',
        'info' => 'Informazione',
        'no_data' => 'Nessun dato disponibile',
        'loading' => 'Caricamento...',
        'saved' => 'Salvato con successo',
        'deleted' => 'Eliminato con successo',
        'updated' => 'Aggiornato con successo',
    ],

    // Forms
    'form' => [
        'required' => 'Obbligatorio',
        'optional' => 'Opzionale',
        'select_option' => 'Seleziona un\'opzione',
        'enter_value' => 'Inserisci valore',
        'choose_file' => 'Scegli file',
    ],

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

    // Messaggi per nuove funzionalità
    'msg.settings_updated_successfully' => 'Impostazioni aggiornate con successo',
    'msg.room_created_successfully' => 'Sala creata con successo',
    'msg.room_updated_successfully' => 'Sala aggiornata con successo',
    'msg.room_deleted_successfully' => 'Sala eliminata con successo',
    'msg.table_created_successfully' => 'Tavolo creato con successo',
    'msg.table_updated_successfully' => 'Tavolo aggiornato con successo',
    'msg.table_deleted_successfully' => 'Tavolo eliminato con successo',
    'msg.cannot_delete_room_has_tables' => 'Non è possibile eliminare una sala che contiene tavoli',
    'msg.required_fields' => 'Campi obbligatori mancanti',
    'msg.invalid_token' => 'Token di sicurezza non valido',
    'msg.not_found' => 'Elemento non trovato',
    'msg.created_successfully' => 'Creato con successo',
    'msg.updated_successfully' => 'Aggiornato con successo',
    'msg.deleted_successfully' => 'Eliminato con successo',
    'msg.error_occurred' => 'Si è verificato un errore',

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

    // ===========================
    // Ordini d'Acquisto (Nuovo)
    // ===========================
    'purchase_order' => [
        'list_title' => 'Ordini d\'Acquisto',
        'create_title' => "Crea Nuovo Ordine d'Acquisto",
        'detail_title' => 'Dettaglio Ordine',
        'receive_title' => 'Ricevi Ordine',
        'back_to_list' => 'Torna alla lista',
        'add_products' => 'Aggiungi Prodotti',
        'ordered_products_title' => 'Prodotti Ordinati',
        'field' => [
            'id' => 'ID',
            'supplier' => 'Fornitore',
            'status' => 'Stato',
            'total' => 'Totale',
            'date' => 'Data',
            'actions' => 'Azioni',
            'product' => 'Prodotto',
            'quantity' => 'Quantità',
            'unit' => 'Unità',
            'sku' => 'SKU',
            'ordered_qty' => 'Qta Ord.',
            'price' => 'Prezzo',
            'discount' => 'Sconto',
            'expiry' => 'Scadenza',
            'received_qty' => 'Qta Ricevuta',
            'barcode' => 'Barcode',
            'notes' => 'Note',
            'supplier_reference' => 'Ref. Fornitore',
            'order_discount' => "Sconto Ordine (%)",
        ],
        'btn' => [
            'add_row' => 'Aggiungi Riga',
            'save_draft' => 'Salva Bozza',
            'new_order' => 'Nuovo Ordine',
            'confirm_receive' => 'Conferma ricezione',
            'download_pdf' => 'Scarica PDF',
            'send_order' => 'Conferma Ordine',
            'mark_as_received' => 'Segna come Ricevuto',
            'edit_order' => 'Modifica Ordine',
        ],
        'placeholder' => [
            'price_per_unit' => '€/pz',
            'discount' => 'sconto',
        ],
        'status' => [
            'draft' => 'Bozza',
            'sent' => 'Confermato',
            'received' => 'Ricevuto',
        ],
        'msg' => [
            'add_at_least_one_product' => 'Aggiungi almeno un prodotto',
            'select_supplier_first' => 'Seleziona prima un fornitore',
            'created_successfully' => 'Ordine creato con successo',
            'not_found' => 'Ordine non trovato',
            'sent_successfully' => 'Ordine inviato con successo',
            'invalid_token' => 'Token non valido',
            'received_successfully' => 'Ordine ricevuto con successo',
            'not_receivable' => 'L\'ordine non è in uno stato ricevibile',
            'updated_successfully' => 'Ordine aggiornato con successo',
            'invalid_branch' => 'Filiale non valida',
            'confirm_send_title' => 'Confermi l\'ordine?',
            'confirm_send_button' => 'Conferma',
            'confirm_receive_title' => 'Segnare come ricevuto?',
            'confirm_receive_button' => 'Conferma',
            'generic_ok' => 'OK',
            'generic_error' => 'Errore',
            'resent_successfully' => 'Email dell\'ordine reinviata con successo',
            'resending_email' => 'Reinvio email ordine...'
            ,'sending_order' => 'Invio ordine...'
            ,'receiving_order' => 'Conferma ricezione...'
        ],
        'summary' => [
            'subtotal' => 'Subtotale',
            'line_discounts' => 'Sconti Righe',
            'order_discount_pct' => 'Sconto Ordine (%)',
            'order_discount_val' => 'Sconto Ordine Val.',
            'net_total' => 'Totale Netto'
        ],
        'history' => [
            'title' => 'Storico Stati',
            'changed_at' => 'Data Cambio',
            'old_status' => 'Stato Precedente',
            'new_status' => 'Nuovo Stato'
        ],
        'validation' => [
            'fix_invalid_prices' => 'Correggi i prezzi non validi',
            'expiry_required' => 'La data di scadenza è obbligatoria per i prodotti indicati',
            'expiry_invalid_format' => 'Formato data scadenza non valido (atteso YYYY-MM-DD)'
        ],
        'pdf' => [
            'title' => 'Ordine d\'Acquisto',
            'footer' => 'Documento generato automaticamente - Non rispondere a questa email'
        ],
        'email' => [
            'subject' => 'Ordine d\'Acquisto #{order}',
            'subject_resend' => 'Ordine d\'Acquisto (Reinvio) #{order}',
            'greeting' => 'Gentile Fornitore,',
            'intro' => 'In allegato trova il nostro ordine d\'acquisto. Può anche scaricarlo cliccando sul pulsante qui sotto:',
            'download_button' => 'Scarica Ordine',
            'thanks' => 'Grazie per la collaborazione,',
            'signature' => 'Ufficio Acquisti'
        ],
        'barcode' => [
            'title' => 'Barcode per Ordine',
            'print_button' => 'Stampa',
            'generated_total' => 'Barcode Generati',
            'none' => 'Nessun barcode generato per questo ordine',
            'invalid_params' => 'Parametri barcode non validi',
            'regenerated' => 'Barcode generati con successo'
        ],
        'stats' => [
            'last_price' => 'Ultimo Prezzo',
            'last_purchase_date' => 'Data Ultimo Acquisto'
        ],
    ],

    // Categorie Prodotti Fornitore & Unità Base
    'supplier_product' => [
        'base_unit' => 'Unità Base',
        'category' => 'Categoria',
        'categories' => [
            'consumables' => 'Consumabili',
            'food' => 'Cibo',
            'raw_materials' => 'Materie Prime',
            'houseware' => 'Casalinghi'
        ],
        'form' => [
            'product' => 'Prodotto',
            'invoice_name' => 'Nome in Fattura',
            'unit' => 'Unità',
            'quantity' => 'Quantità',
            'base_quantity' => 'Quantità in Unità Base (conversione)',
            'price' => 'Prezzo',
            'currency' => 'Valuta'
        ],
        'inventory' => [
            'title' => 'Riepilogo Inventario',
            'supplier_units' => 'Unità Fornitore',
            'base_unit_total' => 'Totale in Unità Base'
        ]
    ],

];
