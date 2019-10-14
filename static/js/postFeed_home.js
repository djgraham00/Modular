$(window).scroll(function () {
    if ($(window).scrollTop() == $(document).height() - $(window).height()) {
        if(this.currentPage === "Posts") {
        var lastID = $('.load').attr('lastID');
        var xhttp = new XMLHttpRequest();

        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                if (this.responseText == 0) {
                    if (!document.getElementById("done")) {
                        document.getElementById("load").style.display = "none";
                        $('section').append("<article id='done' class='post'><h3>No more posts...</h3></article>");
                    }
                } else {
                    $('.load').remove();
                    $('section').append(this.responseText);
                }
            }
        };

        xhttp.open("POST", "./_postFeedAPI_RenderPosts", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");


        xhttp.send(
            "lastID=" + lastID
        );
        }

    }
});