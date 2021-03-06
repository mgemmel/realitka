$(document).ready(function () {
    var isPlaceChanged = false;

    $("#lokalita").on('input', function (e) {

        var option = $('#obce option').filter(function() {
            return this.value === $("#lokalita").val();
        }).val();

        var ID = $('#obce option').filter(function() {
            return this.value === $("#lokalita").val();
        })[0].id;

        if(option !== undefined){
            this.value = option.substr(0, option.indexOf(','));
            isPlaceChanged = true;

            $('#ID').val(ID);
        }
    });

    $("form").submit(function (event) {
        if (!isPlaceChanged) {
            alert("Vyberte jednu z ponúkaných lokalit");
            return false;
        }
        isPlaceChanged = false;
    });
});