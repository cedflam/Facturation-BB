jQuery(document).ready(function () {
    /**
     * Permet d'ajouter et de supprimer une prestation
     */
    let index = $('.acompte').length;
    $('.add_advance').on('click', function () {
        index++;
        let div = $('#invoice_advances');
        //Je récupère le prototype des entrées
        let tmpl = div.data('prototype').replace(/__name__/g, index);
        //j'injecte le code dans la div
        div.append(tmpl);
        // J'initialise les functions
        handleDeleteButton();
        calculFields();

        $('.calcul').removeClass('hidden');
    });

    // Gestion du bouton supprimer
    function handleDeleteButton() {
        //Permet de supprimer une Prestation
        let buttonDelete = $('.delete');
        buttonDelete.on('click', function () {
            $('.calcul').removeClass('hidden');
            let id = $(this).data('target');
            $('#' + id).remove();
        });
    }

    // Calcul des champs
    function calculFields() {
        //J'effectue le calcul
        $('.calcul').on('click', function () {

            let totalTTC = $('.totalTTC').val();
            let restantDu = $('.remainingCapital').val();

            // Je boucle sur les montants
            let amounts = $('.amount');
            let totalAmount = 0;
            amounts.each(function () {
                totalAmount = totalAmount + (+$(this).val());
                restantDu = totalTTC - totalAmount;
                console.log(totalAmount)
                $('.remainingCapital').val(restantDu.toFixed(2));
                $('.totalAdvance').val(totalAmount.toFixed(2));
            });

            // Je cache le bouton une fois le calcul effectué
            $('.calcul').addClass('hidden');
        });
    }
    handleDeleteButton();
    calculFields();
});