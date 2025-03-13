$(document).ready(function(){
    $("form[question-form]").submit(function(event){
        event.preventDefault(); // Empêche la soumission traditionnelle

        var question_id = $(this).attr("question-form");
        var tbodySelector = "#" + question_id + "-body"; // Sélecteur dynamique

        var requestData = {
            question_id: question_id,
            type_de_camion: $(this).find("select[name='type_de_camion']").val(),
            date: $(this).find("input[name='date']").val(),
            nom: $(this).find("input[name='nom']").val(),
            prenom: $(this).find("input[name='prenom']").val(),
            num_permis: $(this).find("input[name='num_permis']").val(),
            poids_total: $(this).find("input[name='poids_total']").val(),
            immatriculation: $(this).find("input[name='immatriculation']").val(),
            ville_d_arrivee: $(this).find("input[name='ville_d_arrivee']").val()
        };

        // Suppression des champs vides pour éviter des erreurs côté serveur
        Object.keys(requestData).forEach(key => {
            if (!requestData[key]) delete requestData[key];
        });

        console.log("Données envoyées :", requestData);

        $.ajax({
            url: "questions.php",
            type: "POST",
            data: requestData,
            dataType: "html",
            success: function(response) {
                console.log("Réponse AJAX : ", response);
                if (response.success) {
                    $(tbodySelector).html(response.html); // Remplace le contenu du tableau
                } else {
                    console.error("Erreur dans la réponse");
                }
            },
            error: function(xhr, status, error) {
                console.error("Erreur AJAX : ", error);
                alert("Une erreur est survenue.");
            }
        });
    });
});
