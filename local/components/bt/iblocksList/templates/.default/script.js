$(document).ready(function() {
    function buttonOnClick(e) {
        let $btnShowMore = $(e.target);
        $.ajax({
            type: 'GET',
            url: window.location.pathname,
            data: {
                "action": "page",
                "page": "page-" + moreButton.dataset.page
            },
            before: function() {
                $btnShowMore.hide();
            },
            success: function(data) {
                $btnShowMore.remove();
                let $data = $(data);
                let $tr = $data.find('.js-table-user-body').children().filter('tr');
                $('.js-table-user-body').append($tr);
                let $btnNewShowMore = $data.find('.js-show-more');
                if ($btnNewShowMore.length) {
                    $('.js-table-container').append($btnNewShowMore);
                }
            },
            error: function () {
                $btnShowMore.show();
            }
        });
    }

    $('.js-table-container').on('click', '.js-show-more', buttonOnClick);
});