jQuery(document).ready(function ($) {
    $('#dv-import-form').on('submit', function (e) {
        e.preventDefault();
        var formData = new FormData();
        formData.append('action', 'dv_handle_import');
        formData.append('security', dv_ajax.nonce);
        formData.append('file', $('#import_file')[0].files[0]);

 $('#import-loader').show(); // Show loader
        $('#import-result').html('');

        $.ajax({
            url: dv_ajax.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            xhr: function () {

                var xhr = new window.XMLHttpRequest();
                return xhr;
            },
            success: function (response) {
                 $('#import-loader').hide(); // Hide loader
                $('#import-result').html('<p>Imported</p>');
                
            },
            error: function () {
                 $('#import-loader').hide(); // Hide loader
                $('#import-result').html('<p style="color:red;">AJAX error occurred.</p>');
            }
        });
    });
});
