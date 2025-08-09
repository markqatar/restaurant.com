$(document).ready(function(){
  const { urls, csrfToken, supplierId } = EDIT_PO_VARS;

  function newRow(){
    const currencyOptions = (EDIT_PO_VARS.currencies||[]).map(c=>`<option value="${c}" ${c===EDIT_PO_VARS.defaultCurrency?'selected':''}>${c}</option>`).join('');
    return `<tr>
      <td><select name="products[]" class="form-control select2 product-select" required></select></td>
      <td><input type="number" step="0.01" name="quantities[]" class="form-control" required></td>
      <td><select name="units[]" class="form-control select2 unit-select" disabled></select><input type="hidden" name="units[]" value=""></td>
      <td><input type="number" step="0.01" name="prices[]" class="form-control"></td>
      <td><select name="currencies[]" class="form-select">${currencyOptions}</select></td>
      <td class="text-center"><button type="button" class="btn btn-danger btn-sm removeRow"><i class="fas fa-trash"></i></button></td>
    </tr>`;
  }

  function initProductSelect($el){
    $el.select2({
      ajax: {
        url: EDIT_PO_VARS.urls.productsSelect,
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: params => ({ search: params.term, csrf_token: csrfToken, supplier_id: supplierId }),
        processResults: data => ({ results: data.map(p => ({ id: p.id, text: p.text, unit_id: p.unit_id, unit_name: p.unit_name })) })
      },
  placeholder: EDIT_PO_VARS.translations.product_placeholder || 'Product'
    }).on('select2:select', function(e){
      const d = e.params.data;
      const $row = $(this).closest('tr');
      const $unitSel = $row.find('.unit-select');
      const $hidden = $row.find('input[type=hidden][name="units[]"]').last();
      if(d.unit_id){
        $unitSel.html('').append(new Option(d.unit_name || d.unit_id, d.unit_id, true, true));
        $hidden.val(d.unit_id);
      }
    });
  }

  // Initialize existing product selects
  $('.product-select').each(function(){ initProductSelect($(this)); });

  $('#addItem').on('click', function(){
    if(!supplierId){
  Swal.fire(EDIT_PO_VARS.translations.generic_error || 'Error', EDIT_PO_VARS.translations.select_supplier_first, 'warning');
      return;
    }
    $('#orderItemsTable tbody').append(newRow());
    initProductSelect($('#orderItemsTable tbody tr:last .product-select'));
  });

  $(document).on('click','.removeRow', function(){ $(this).closest('tr').remove(); });

  $('#purchaseOrderEditForm').on('submit', function(e){
    e.preventDefault();
    $.ajax({
      url: urls.update,
      type: 'POST',
      data: $(this).serialize(),
      dataType: 'json',
      success: res => {
        if(res.success){
          Swal.fire(EDIT_PO_VARS.translations.generic_ok || 'OK', res.message, 'success').then(()=> location.href = '/admin/suppliers/purchaseorders/view/'+ urls.update.split('/').pop());
        } else {
          Swal.fire(EDIT_PO_VARS.translations.generic_error || 'Error', res.message || 'Error', 'error');
        }
      }
    });
  });
});
