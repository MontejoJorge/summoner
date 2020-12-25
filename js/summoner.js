$(document).ready(function () {
    $("#reveralMasteries").change(reveralMaestries);
});
function reveralMaestries(){
    if(this.checked) {
        $("#championsMasteries").css("display","grid");
        $("#flex-info").css("visibility","hidden");
    } else {
        $("#championsMasteries").css("display","none");
        $("#flex-info").css("visibility","visible");
    }
}