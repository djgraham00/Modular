function addItemToTicket(){
        $.ajax({
            url: './src/action/ticketItem/add.php',
            type: 'POST',
            data: {
                'UPC': $('[name=upc]').val(),
            },
            dataType: 'json',
            success: function (data) {
                alert("Item Added");
                if (data.success === true) {
                    window.location.reload();
                } else {
                    alert("Error");
                }
            },
            error: function (request, error) {
                alert("Request: " + JSON.stringify(request));
            }
        });
}
