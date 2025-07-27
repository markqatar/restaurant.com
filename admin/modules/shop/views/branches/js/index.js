$(document).ready(function() {
    // Initialize DataTable
    $('#branchesTable').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/it-IT.json'
        },
        order: [[0, 'asc']],
        pageLength: 25,
        responsive: true
    });
});

function confirmDelete(id, name) {
    $('#branchName').text(name);
    $('#deleteConfirm').attr('href', 'branches/delete/' + id);
    $('#deleteModal').modal('show');
}