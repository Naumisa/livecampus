// Enabling user edit form
function enableUserEditForm(){
    const inputs = document.querySelectorAll('input');

    inputs.forEach(function(input){
        input.removeAttribute('disabled');
    });
    document.getElementById('cancel').removeAttribute('hidden');
    document.getElementById('submit').removeAttribute('hidden');
    document.getElementById('edit').setAttribute('hidden', true);
    inputs[0].focus();
};

// Disable user edit form (cancel editing)
function disableUserEditForm(){
    const inputs = document.querySelectorAll('input');

    inputs.forEach(function(input){
        input.setAttribute('disabled', true);
    });
    document.getElementById('cancel').setAttribute('hidden', true);
    document.getElementById('submit').setAttribute('hidden', true);
    document.getElementById('edit').removeAttribute('hidden');
};

// Set the good ID for the file user wanted to share.
function shareFile(id) {
    const inputs = document.querySelectorAll('input');

    inputs.forEach(function(input) {
        if (input.name === 'file') {
            input.setAttribute('value', id);
        }
    })
}
