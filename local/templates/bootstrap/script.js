$( document ).ready(function() {

    $("#geolocationSend").click(function () {
        let adressInput = $("#adressText")[0];
        if (adressInput.value === "") {
            adressInput.classList.add("is-invalid");
        } else {
            adressInput.classList.remove("is-invalid");

            $.ajax({
                type: 'POST',
                url: window.location.pathname + "?action=search",
                data: {
                    "name": adressInput.value
                },
                success: function(data) {
                    console.log("success, data: " + data);
                }
            });


            $("#coordsResult").collapse('show');
        }
    });
});