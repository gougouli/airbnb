$(function () {
    // NAME AUTO-COMPLETE
    $('#place').autocomplete({
        source: function (request, response) {
            $.ajax({
                type: "POST",
                url: "../../App/autocomplete.php",
                data: {
                    term: request.term,
                    type: "city"
                },
                success: response,
                dataType: 'json',
                minLength: 2,
                delay: 100
            });
        }
    });
});
