let checkValid = (data) => {
    let regExp = /[0-9]{4} [\w\s]+, [\w\s]+, [\w]+, [0-9]{5}/i;
    return regExp.test(data);
};


$(document).ready(function() {
    $("#geolocationSend").click(function () {
        let adressInput = document.getElementById("adressText");
        if (adressInput.value === "" || checkValid(adressInput.value) === false) {
            adressInput.classList.add("is-invalid");
            adressInput.value = "Адрес некорректен (пример: 9355 Burton Way, Beverly Hills, ca, 90210)";
        } else {

            $.ajax({
                type: 'POST',
                url: window.location.pathname + "?action=search",
                data: {
                    "name": adressInput.value
                },
                success: function(data) {
                    data = JSON.parse(data);
                    if (data.status === "success") {
                        adressInput.classList.remove("is-invalid");
                        document.getElementById("resultLat").value = data.latitude;
                        document.getElementById("resultLong").value = data.longitude;
                        $("#coordsResult").collapse('show');
                    } else {
                        adressInput.classList.add("is-invalid");
                        adressInput.value = "Город не найден";
                    }

                }
            });
        }
    });
});