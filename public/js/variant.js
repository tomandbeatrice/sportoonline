$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#variantForm').submit(function(e) {
        e.preventDefault();

        let formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#alertBox')
                    .html('<div class="alert alert-success">Varyasyon eklendi ✅</div>')
                    .fadeIn()
                    .delay(2000)
                    .fadeOut();
                $('#variantForm')[0].reset();
            },
            error: function(xhr) {
                $('#alertBox')
                    .html('<div class="alert alert-danger">Hata oluştu ❌</div>')
                    .fadeIn()
                    .delay(2500)
                    .fadeOut();
                console.error(xhr.responseText);
            }
        });
    });
});