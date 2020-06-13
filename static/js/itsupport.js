function addTicket() {

    $.ajax({
        url: './_Ticket_Add',
        type: 'POST',
        data: {
            'title': $('[name=title]').val(),
            'priority': $('[name=priority]').val(),
            'site': $('[name=site]').val(),
            'room': $('[name=room]').val(),
            'body': $('[name=body]').val()
        },
        dataType: 'json',
        success: function (data) {

            if(data.success === true){
               addAlert("warning", "Your ticket has been added to the queue.");
               $('[name=title]').val('');
               $('[name=room]').val('');
               $('[name=body]').val('');
            }
            else {
                addAlert("warning", "Unable to create ticket. Please email <a href=\"mailto:djgraham@p-t.k12.ok.us\">djgraham@p-t.k12.ok.us</a> for assistance.");
            }
        },
        error: function (request, error) {
            console.log(request);
        }
    });
}

function closeTicket(id) {
    $.ajax({
        url: './closeTicket',
        type: 'POST',
        data: {
            'id': id
        },
        dataType: 'json',
        success: function (data) {
            console.log(data);
            if(data.success === true){
                window.location.replace ("./viewTickets");
            } else{
                alert("Error closing ticket.. Please contact your system administrator.");
            }
        },
        error: function (request, error) {
            console.log(request);
        }
    });
}