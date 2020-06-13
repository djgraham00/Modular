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
      console.log(redirectLocation);
    $.ajax({
        url: './Login',
        type: 'POST',
        data: {
            'username': $('[name=username]').val(),
            'password': $('[name=password]').val()
        },
        dataType: 'json',
        success: function (data) {
            console.log(data);
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