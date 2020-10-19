function deAuth(){
    $.ajax({
          url: './Logout',
          type: 'POST',
          success: function (data) {
              if(data.success === true){
                 window.location.replace ("./Login");
              }
          },
          error: function (request, error) {
              console.log(request);
          }
      });
  }

  function login(redirectLocation) {
    $.ajax({
        url: './Login',
        type: 'POST',
        data: {
            'username': $('[name=username]').val(),
            'password': $('[name=password]').val()
        },
        dataType: 'json',
        success: function (data) {
            if(data.success === true){
               window.location.replace (redirectLocation);
            }else{
                addAlert("danger", "Invalid Username or Password...");
            }
        },
        error: function (request, error) {
            console.log(request);
        }
    });
}

function createAccount() {

    if($('[name=password]').val() != $('[name=password_confirm]').val()) {
        addAlert("danger", "Passwords do not match");
    } else {

        $.ajax({
            url: './Register',
            type: 'POST',
            data: {
                'firstName': $('[name=firstName]').val(),
                'lastName': $('[name=lastName]').val(),
                'username': $('[name=username]').val(),
                'password': $('[name=password]').val()
            },
            dataType: 'json',
            success: function (data) {
                console.log(data);
                if (data.success === true) {
                    window.location.replace("./Login");
                } else {
                    addAlert("danger", data.msg);
                }
            },
            error: function (request, error) {
                console.log(request);
            }
        });
    }
}