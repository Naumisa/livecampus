// Enabling user edit form
function enableUserEditForm(){
    const username_input = document.getElementById('username');
    
    username_input.removeAttribute('disabled');
    username_input.focus();
    document.getElementById('email').removeAttribute('disabled');
    document.getElementById('cancel').removeAttribute('hidden');
    document.getElementById('submit').removeAttribute('hidden');
    document.getElementById('edit').setAttribute('hidden', true);
};

// Disable user edit form (cancel editing)
function disableUserEditForm(){
    document.getElementById('username').setAttribute('disabled', true);
    document.getElementById('email').setAttribute('disabled', true);
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