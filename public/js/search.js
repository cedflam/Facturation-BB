$('document').ready(function () {
    /**
     * Permet de faire une recherche instantanée
     */
    $('.search').on("keyup", function() {
        let value = $(this).val().toLowerCase();
        $(".card").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
});