function deleteRole(roleID, cwid) {

    $.ajax({
        url: './_EditRoles_Remove',
        type: 'POST',
        data: {
            'CWID': cwid,
            'RoleID': roleID
        },
        dataType: 'json',
        success: function (data) {
            console.log(data);
            if(data.success === true){
                window.location.reload();
            }
        },
        error: function (request, error) {
            console.log(request);
        }
    });
}

function addRole(cwid) {

    $.ajax({
        url: './_EditRoles_Add',
        type: 'POST',
        data: {
            'CWID': cwid,
            'RoleID': $('[name=RoleID]').val()
        },
        dataType: 'json',
        success: function (data) {
            console.log(data);
            if(data.success === true){
                window.location.reload();
            }
        },
        error: function (request, error) {
            console.log(request);
        }
    });
}

function changePassword(cwid) {
    var requireReset;
   if(document.getElementById('requireReset').checked) {
       requireReset = "true";
   }
   else {
       requireReset = "false";
   }

    $.ajax({
        url: './passwd',
        type: 'POST',
        data: {
            'CWID': cwid,
            'requireReset' : requireReset,
            'password': $('[name=password]').val()
        },
        dataType: 'json',
        success: function (data) {
            console.log(data);
            if(data.success === true){
               addAlert("success", "Password successfully changed...")
                $('[name=password]').val('');
            } else {
                addAlert("danger", "An error occurred while attempting to change the password...")
            }
        },
        error: function (request, error) {
            console.log(request);
        }
    });
}