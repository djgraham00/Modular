function addAlert(type, message) {
    var alertTemplate = "" +
        "<div class=\"alert alert-"+ type +" alert-dismissible fade show\" style=\"textl-align: left;\" role=\"alert\">" +
                        message +
        "  <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">" +
        "    <span aria-hidden=\"true\">&times;</span>" +
        "  </button>" +
        "</div>";

    $("#errView").append(alertTemplate);
}