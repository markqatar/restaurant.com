$(document).ready(function () {
    const { urls, csrfToken } = PO_VARS;

    $('#purchaseOrdersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: urls.datatable,
            type: 'POST',
            data: { csrf_token: csrfToken }
        },
        columns: [
            { data: 'id' },
            { data: 'supplier_name' },
            { data: 'status' },
            { data: 'total_amount' },
            { data: 'created_at' },
            {
                data: 'id',
                render: id => `
                    <a href="/admin/suppliers/purchaseorders/view/${id}" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                    <a href="/admin/suppliers/purchaseorders/edit/${id}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                `
            }
        ]
    });
});