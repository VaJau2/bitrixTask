$(document).ready(function() {
    function buttonOnClick() {
        $.ajax({
            type: 'GET',
            url: window.location.pathname,
            data: {
                "action": "page",
                "page": "page-" + moreButton.dataset.page
            },
            success: function(data) {
                data = JSON.parse(data);
                //выключаем кнопку "ещё", если новые строки закончились
                if (data.nav) {
                    //кнопка заменяется на новую, и к ней присоединяется onlick-функция
                    moreButton.outerHTML = data.nav;
                    moreButton = document.getElementById("moreButton");
                    moreButton.onclick = () => { buttonOnClick() };
                } else {
                    moreButton.style.display = "none";
                }

                //добавляем в таблицу новые строки
                let pageData = data.pageData;
                let tableBody = document.getElementById("iblocksTableBody");
                for(let tableRow of Object.values(pageData.i1)) {
                    let newRow = tableBody.insertRow();

                    //th-элемент не добавляется через insertCell :с
                    let nameHeader = document.createElement("th");
                    nameHeader.innerHTML = tableRow.NAME;
                    nameHeader.rowSpan = tableRow.ARRAY_COUNT;
                    nameHeader.scope = "row";
                    newRow.appendChild(nameHeader);

                    let dateCell = newRow.insertCell();
                    let dateText = document.createTextNode(tableRow.DATE);
                    dateCell.appendChild(dateText);
                    dateCell.rowSpan = tableRow.ARRAY_COUNT;

                    if (tableRow.USERS.length > 0) {
                        let userCell = newRow.insertCell();
                        let userText = document.createTextNode(pageData.USERS[tableRow.USERS[0]]);
                        userCell.appendChild(userText);
                    }

                    if (tableRow.ELEMENTS.length > 0) {
                        let elementCell = newRow.insertCell();
                        let elementText = document.createTextNode(pageData.i2[tableRow.ELEMENTS[0]]);
                        elementCell.appendChild(elementText);
                    }

                    for(let i = 1; i < tableRow.ARRAY_COUNT; i++) {
                        let newUnderRow = tableBody.insertRow();

                        let userCell = newUnderRow.insertCell();
                        if (tableRow.USERS.length > i) {
                            let userText = document.createTextNode(pageData.USERS[tableRow.USERS[i]]);
                            userCell.appendChild(userText);
                        }

                        let elementCell = newUnderRow.insertCell();
                        if (tableRow.ELEMENTS.length > i) {
                            let elementText = document.createTextNode(pageData.i2[tableRow.ELEMENTS[i]]);
                            elementCell.appendChild(elementText);
                        }

                    }
                }
            }
        });
    }

    let moreButton = document.getElementById("moreButton");
    moreButton.onclick = () => { buttonOnClick() };
});