document.addEventListener('DOMContentLoaded', function () {
    if (!USER_FORM_VARS) return;

    const { translations } = USER_FORM_VARS;

    // ✅ Form validation
    const form = document.getElementById('userForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            if (!validateForm('userForm')) {
                e.preventDefault();
                Swal.fire({
                    title: translations.errorTitle,
                    text: translations.requiredField,
                    icon: 'error'
                });
            }
        });
    }

    // ✅ Branch selection logic
    const branchCheckboxes = document.querySelectorAll('.branch-checkbox');
    branchCheckboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            const branchId = this.value;
            const primaryRadio = document.getElementById('primary_' + branchId);

            if (this.checked) {
                primaryRadio.disabled = false;

                // If this is the first branch selected, automatically set as primary
                const checkedBoxes = document.querySelectorAll('.branch-checkbox:checked');
                if (checkedBoxes.length === 1) {
                    primaryRadio.checked = true;
                }
            } else {
                primaryRadio.disabled = true;
                primaryRadio.checked = false;

                // If there's only one branch left selected, make it primary
                const checkedBoxes = document.querySelectorAll('.branch-checkbox:checked');
                if (checkedBoxes.length === 1) {
                    const lastBranchId = checkedBoxes[0].value;
                    document.getElementById('primary_' + lastBranchId).checked = true;
                }
            }
        });
    });
});