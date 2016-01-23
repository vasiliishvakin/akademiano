function previewImage(name, dataUri) {
    name = name.replace(".", "_");
    var div = $(document.createElement("div"));
    var img = $(document.createElement("img"));
    img.attr("src", dataUri);
    img.width(250);
    img.height(250);
    img.css("margin", "3px");

    var labelTitle = $(document.createElement("label"));
    labelTitle.attr("for", "filesTitle_" + name);
    labelTitle.text("Title");
    labelTitle.addClass("control-label col-sm-2");

    var title = $(document.createElement("input"));
    title.attr("type", "text");
    title.attr("name", "filesData[title][" + name + "]");
    title.attr("id", "filesTitle_" + name);
    title.addClass("form-control");

    var labelDescription = $(document.createElement("label"));
    labelDescription.attr("for", "filesDescription_" + name);
    labelDescription.text("Description");

    var description = $(document.createElement("textarea"));
    description.attr("name", "filesData[description][" + name + "]");
    description.attr("id", "filesDescription_" + name);
    description.addClass("form-control");

    var labelMain = $(document.createElement("label"));
    labelMain.attr("for", "filesMain_" + name);
    labelMain.text("Главное");

    var main = $(document.createElement("input"));
    main.attr("type", "radio");
    main.attr("name", "filesData[main]");
    main.val(name);
    main.attr("id", "filesMain_" + name);
    main.addClass("form-control");

    var labelOrder = $(document.createElement("label"));
    labelOrder.attr("for", "filesOrder_" + name);
    labelOrder.text("Порядок");

    var order = $(document.createElement("input"));
    order.attr("type", "number");
    order.attr("name", "filesData[order][" + name + "]");
    order.attr("id", "filesOrder_" + name);
    order.addClass("form-control");

    var labelInfo = $(document.createElement("label"));
    labelInfo.attr("for", "filesInfo_" + name);
    labelInfo.text("Info");

    var info = $(document.createElement("textarea"));
    info.attr("name", "filesData[info][" + name + "]");
    info.attr("id", "filesInfo_" + name);
    info.addClass("form-control");


    var divRowT = $(document.createElement("div"));
    divRowT.addClass("row");
    var divct = $(document.createElement("div"));
    divct.addClass("col-md-12, text-center");
    divct.html("<h3>Новые файлы:</h3>");
    divct.appendTo(divRowT);

    var divRow = $(document.createElement("div"));
    divRow.addClass("row");
    var divcl = $(document.createElement("div"));
    divcl.addClass("col-md-3");
    divcl.appendTo(divRow);
    var divcr = $(document.createElement("div"));
    divcr.addClass("col-md-8");
    divcr.appendTo(divRow);

    img.appendTo(divcl);

    labelTitle.appendTo(divcr);
    title.appendTo(divcr);
    labelDescription.appendTo(divcr);
    description.appendTo(divcr);
    labelMain.appendTo(divcr);
    main.appendTo(divcr);
    labelOrder.appendTo(divcr);
    order.appendTo(divcr);
    labelInfo.appendTo(divcr);
    info.appendTo(divcr);

    divRowT.appendTo("#preview");

    divRow.appendTo("#preview");
};

$(document).ready(function () {
    $(document).on('change', '#files', function () {
        var preview = $("#preview");
        preview.html('');
        var files = $("#files")[0].files;
        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            var reader = new FileReader();
            reader.onload = function (event) {
                var dataUri = event.target.result;
                previewImage(file.name, dataUri);
            };
            reader.readAsDataURL(file);
        }
    });
});