function deAuth(){
    $.ajax({
          url: './_coreAuthAPI_deAuth',
          type: 'POST',
          success: function (data) {
              if(data.success === true){
                 window.location.replace ("./login");
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
        url: './_coreAuthAPI_auth',
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
                document.getElementById("err").innerHTML = "Invalid Username or Password";
            }
        },
        error: function (request, error) {
            console.log(request);
        }
    });
}