<div class="card">
  <div class="card-header">
    <h5>Prodotti del Fornitore</h5>
    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addProductModal">
      <i class="fas fa-plus"></i> Aggiungi Prodotto
    </button>
  </div>
  <div class="card-body">
    <table id="supplierProductsTable" class="table table-striped table-bordered w-100">
      <thead>
        <tr>
          <th>Prodotto</th>
          <th>Nome Fornitore</th>
          <th>Unità</th>
          <th>Quantità</th>
          <th>Prezzo</th>
          <th>Azioni</th>
        </tr>
      </thead>
    </table>
  </div>
</div>

<!-- Modale -->
<div class="modal fade" id="addProductModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form id="addSupplierProductForm">
      <input type="hidden" name="supplier_id" value="<?php echo $supplier_id; ?>">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Aggiungi Prodotto</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Prodotto</label>
            <select name="product_id" class="form-control select2"></select>
          </div>
          <div class="mb-3">
            <label>Nome su fattura</label>
            <input type="text" name="supplier_name" class="form-control">
          </div>
          <div class="mb-3">
            <label>Unità</label>
            <select name="unit_id" class="form-control select2"></select>
          </div>
          <div class="mb-3">
            <label>Quantità</label>
            <input type="number" step="0.01" name="quantity" class="form-control">
          </div>
          <div class="mb-3">
            <label>Prezzo</label>
            <input type="number" step="0.01" name="price" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Salva</button>
        </div>
      </div>
    </form>
  </div>
</div>