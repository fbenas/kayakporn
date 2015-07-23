$(document).ready(function() {
    $('img').click(function() {
        id = this.id;
        $.ajax({
            url: 'api.php',
            data: {'id': id},
            method: 'get',
        }).done(function(data) {
            if (data == false) {
                return;
            }
            // Remove all current Iframs
            // Unhide all images
            $('img').removeClass('hidden');
            $('div iframe').remove();

            // Now hide the clicked image
            // And show the clicked iframe
            $('#div-' + id).append(data);
            $('iframe').load(function() {
                $('#div-' + id + ' img').addClass('hidden');
                $(this).removeClass('hidden');
            });
        });
    });
});