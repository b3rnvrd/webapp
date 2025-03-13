$(document).ready(function(){
    $("form[data-table]").submit(function(event){
        event.preventDefault(); // Empêche la soumission traditionnelle

        var donnee = $(this).find("input[name='donnee']").val();
        var attribut = $(this).find("select[name='attribut']").val();
        var tableId = $(this).attr("data-table");

        if (!donnee || !attribut || !tableId) {
            alert("Veuillez remplir tous les champs.");
            return;
        }

        console.log("Données envoyées :", {donnee, attribut, table_id: tableId});

        $.ajax({
            url: "main_ajax.php",
            type: "POST",
            data: {donnee: donnee, attribut: attribut, table_id: tableId},
            dataType: "html",
            success: function(response) {
                console.log("Réponse AJAX : ", response);
                $("#" + tableId + " tbody").html(response);
            },
            error: function(xhr, status, error) {
                console.error("Erreur AJAX : ", error);
                alert("Une erreur est survenue.");
            }
        });
    });
});
