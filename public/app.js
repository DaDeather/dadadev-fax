$(document).ready(function () {
    const LOAD_INTERVAL = 10000;

    $('.datetime').each(function (index, nativeElement) {
        let element = $(nativeElement);
        let formattedDateTime = moment(element.text()).toDate().toLocaleString();

        element.text(formattedDateTime);
    });

    setInterval(function () {
        refreshList($('#received-faxes tbody').first(), URL_API_JSON_RECEIVED_FAXES);
    }, LOAD_INTERVAL);

    setInterval(function () {
        refreshList($('#sent-faxes tbody').first(), URL_API_JSON_SENT_FAXES);
    }, LOAD_INTERVAL);
});

function refreshList(elListToUpdate, urlToLoad) {
    $.getJSON(urlToLoad, function (receivedData) {
        let faxesToAdd = '';
        $.each(receivedData.data, function (index, fax) {
            let faxDate = '';
            if (typeof fax.updated !== 'undefined' && fax.updated !== null) {
                faxDate = moment(fax.updated).toDate().toLocaleString();
            } else {
                faxDate = moment(fax.created).toDate().toLocaleString();
            }

            let displayLink = '';
            if (typeof fax.localFilePath !== 'undefined'
                && fax.localFilePath !== null
                && -1 === FAX_FINISHED_RECEIVE_FAILED_STATE_COLLECTION.indexOf(fax.faxStatus)
            ) {
                displayLink = '<a class="btn btn-success" target="_blank" ' +
                    'href="/fax/file/' + fax.id + '">' +
                    '<i class="icon ion-md-open"></i>' +
                    '</a>'
            } else if(-1 === FAX_FINISHED_RECEIVE_FAILED_STATE_COLLECTION.indexOf(fax.faxStatus)) {
                displayLink = '<span title="' + TRANS_FAX_IS_BEING_RECEIVED + '" class="btn btn-success"><i class="fa fa-spinner fa-spin"></i></span>';
            }

            let phoneNumber = '';
            if (typeof fax.faxDirection !== 'undefined' && fax.faxDirection !== null) {
                if (fax.faxDirection === 'inbound') {
                    phoneNumber = (fax.fromPhoneNumber === null ? '' : fax.fromPhoneNumber);
                } else {
                    phoneNumber = (fax.toPhoneNumber === null ? '' : fax.toPhoneNumber);
                }
            }

            faxesToAdd += '<tr>' +
                '<th scope="row">' + (index + 1) + '</th>' +
                '<td>' + phoneNumber + '</td>' +
                '<td>' + (fax.faxStatus === null ? '' : fax.faxStatus) + '</td>' +
                '<td>' + faxDate + '</td>' +
                '<td>' + displayLink + '</td>' +
                '</tr>';
        });

        elListToUpdate.find('tr').remove();
        elListToUpdate.append(faxesToAdd);
    });
}