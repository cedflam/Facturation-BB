jQuery(document).ready(function () {
    /**
     * Permet d'ajouter et de supprimer une prestation
     */
    let index = $('.description').length;
    $('.add_prestation').on('click', function () {
        index++;
        let div = $('#estimate_descriptions');

        //Je récupère le prototype des entrées
        let tmpl = div.data('prototype').replace(/__name__/g, index);
        //j'injecte le code dans la div
        div.append(tmpl);
        handleDeleteButton();
        calculField()
    });

    // Gestion du bouton supprimer
    function handleDeleteButton() {
        //Permet de supprimer une Prestation
        let buttonDelete = $('.delete');
        buttonDelete.on('click', function () {
            let id = $(this).data('target');
            $('#' + id).remove();
        });
    }

    // Je calcul les champs
    function calculField() {
        let buttonCalcul = $('.calcul');
        buttonCalcul.on('click', function () {
            //Je récupère les variables
            let id = $(this).data('target');
            let unitPrice = ($('#' + id + '_unitPrice').val());
            let quantity = ($('#' + id + '_quantity').val());
            let tva = ($('#' + id + '_tva').val());

            let montantHt = unitPrice * quantity;
            let montantTtc = (montantHt * tva) / 100 + montantHt;
            console.log('id='+id, 'unitPrice='+unitPrice, 'quantity='+quantity, 'tva='+tva, 'montantHT='+montantHt, 'montantTTC='+montantTtc);
            // J'injecte la valeur dans le champ
            $('#' + id + '_totalTtc').val(montantTtc.toFixed(2));
            $('#' + id + '_totalHt').val(montantHt.toFixed(2));

            // Total TTC
            let totalTtc = $('.totalTtc');
            let total = 0;
            totalTtc.each(function (index) {
                total = total + (+$(this).val());
                $('#estimate_totalTtc').val(total.toFixed(2));
            })

            // total HT
            let fieldTotalHt = $('.totalHt');
            let totalHt = 0;
            fieldTotalHt.each(function (index) {
                totalHt = totalHt + (+$(this).val());
                $('#estimate_totalHt').val(totalHt.toFixed(2));
            })
        })
    }

    calculField();
    handleDeleteButton();
});