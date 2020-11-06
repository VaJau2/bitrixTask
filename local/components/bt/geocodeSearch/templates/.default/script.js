let checkValid = (data) => {
    let regExp = /[0-9]{4} [\w\s]+, [\w\s]+, [\w]+, [0-9]{5}/i;
    return regExp.test(data);
};
console.log("test log");

$(document).ready(function() {
    $("#geolocationSend").click(function () {
        let adressInput = document.getElementById("adressText");
        let errorLabel = document.getElementById("errorLabel");
        if (adressInput.value === "" || checkValid(adressInput.value) === false) {
            adressInput.classList.add("is-invalid");
            errorLabel.style.display = "block";
            errorLabel.innerHTML = "Форма поиска не соответствует требованиям " +
                "(прим. 9355 Burton Way, Beverly Hills, ca, 90210)";
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
                        errorLabel.style.display = "none";
                        document.getElementById("resultLat").value = data.latitude;
                        document.getElementById("resultLong").value = data.longitude;
                        $("#coordsResult").collapse('show');
                    } else {
                        adressInput.classList.add("is-invalid");
                        errorLabel.style.display = "block";
                        errorLabel.innerHTML = "Город не найден";
                    }

                }
            });
        }
    });
});