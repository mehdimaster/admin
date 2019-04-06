var total_photos_counter = 0;
var name = "";
Dropzone.autoDiscover = false;

var myDropzone = new Dropzone(".dropzone", {
    url: "images-add",
    uploadMultiple: true,
    parallelUploads: 2,
    maxFilesize: 16,
    previewTemplate: document.querySelector('#preview').innerHTML,
    addRemoveLinks: true,
    dictRemoveFile: 'Remove file',
    dictFileTooBig: 'Image is larger than 16MB',
    timeout: 10000,
    init: function () {
        this.on("removedfile", function (file) {
            $.post({
                url: 'images-delete',
                data: {id: file.customName, _token: $('[name="_token"]').val()},
                dataType: 'json',
                success: function (data) {
                    total_photos_counter--;
                    $("#counter").text("# " + total_photos_counter);
                }
            });
        });
    },
    renameFile: function (file) {
        name = new Date().getTime() + Math.floor((Math.random() * 100) + 1) + '_' + file.name;
        return name;
    },
    init: function () {
        this.on("removedfile", function (file) {
            $.post({
                url: 'images-delete',
                data: {id: file.customName, _token: $('[name="_token"]').val()},
                dataType: 'json',
                success: function (data) {
                    total_photos_counter--;
                    $("#counter").text("# " + total_photos_counter);
                }
            });
        });
    },
    success: function (file, done) {

        total_photos_counter++;
        $("#counter").text("# " + total_photos_counter);
        file["customName"] = name;

        $(".images-id").val($(".images-id").val()+','+done.toString());

    }
});

