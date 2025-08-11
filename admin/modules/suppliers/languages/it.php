<?php
return [
    // (Navigation & generic UI keys pruned - now global)
    // Keep supplier domain + purchase orders + inventory transfer + product categories + errors

    'error' => [
        '404_title' => 'Pagina Non Trovata',
        '404_message' => 'La pagina che stai cercando non è stata trovata. Controlla l\'URL o torna alla dashboard.',
    ],
    'page_not_found' => 'Pagina Non Trovata',
    'back_to_dashboard' => 'Torna alla Dashboard',

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

    // Ordini d'Acquisto
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
            'supplier_invoice_pdf' => 'PDF Fornitore',
            'view_pdf' => 'Vedi PDF',
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

    // Trasferimenti Inventario & Motivi
    'inventory_transfer' => [
        'menu_title' => 'Trasferimenti Inventario',
        'list_title' => 'Trasferimenti Inventario',
        'new_transfer' => 'Nuovo Trasferimento',
        'from_branch' => 'Da Filiale',
        'to_branch' => 'A Filiale',
        'item' => 'Voce',
        'quantity' => 'Quantità',
        'unit' => 'Unità',
        'note' => 'Nota',
        'created_at' => 'Creato il',
        'reason_out' => 'Uscita Trasferimento',
        'reason_in' => 'Entrata Trasferimento',
        'msg' => [
            'completed' => 'Trasferimento completato',
            'failed' => 'Trasferimento fallito',
            'invalid' => 'Dati trasferimento non validi'
        ]
    ],
    'inventory' => [
        'reason' => [
            'transfer_out' => 'Trasferimento Uscita',
            'transfer_in' => 'Trasferimento Entrata'
        ]
    ],

];
