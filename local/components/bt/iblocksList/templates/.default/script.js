$(document).ready(function() {
    let moreButton = document.getElementById("moreButton");
    moreButton.onclick = () => {

        $.ajax({
            type: 'POST',
            url: window.location.pathname + "?action=page",
            data: {
                "pageNum": moreButton.dataset.page
            },
            success: function(data) {
                console.log("do you see new page?..");
            }
        });
    }
});