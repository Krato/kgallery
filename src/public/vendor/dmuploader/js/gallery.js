(function( $, window, undefined ) {
    $.gallery = $.extend( {}, {

        addLog: function(str){
            console.log(str);
        },

        addFile: function(id, i, file){


            var template = '<div id="file-' + i + '" class="col-md-1 img-upload">' +
                '<img src="http://placehold.it/48.png" class="img-responsive" />' +
                '<div class="full-width">' +
                '<div class="progress m-b-5">'+
                '<div class="progress-bar progress-bar-complete" style="width:0px"></div>'+
                '</div>'+
                '</div>' +
                '</div>';

            var i = $(id).attr('file-counter');
            if (!i){
                $(id).empty();
                i = 0;
            }

            i++;

            $(id).attr('file-counter', i);

            $(id).prepend(template);
        },

        updateFileStatus: function(i, status, message){
            $('#file-' + i).find('span.demo-file-status').html(message).addClass('demo-file-status-' + status);
        },

        updateFileProgress: function(i, percent){
            $('#file-' + i).find('div.progress-bar').width(percent);
        },

        humanizeSize: function(size) {
            var i = Math.floor( Math.log(size) / Math.log(1024) );
            return ( size / Math.pow(1024, i) ).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i];
        },

        addFileUploaded : function(id, file, delete_ul){
            var template = '<div class="col-xs-6 col-md-3 photo-item sortable" id="photo_'+ id +'">' +
                '<div class="panel panel-default">' +
                '<div class="panel-image">' +
                '<img src="'+ file +'" class="panel-image-preview " />' +
                '<label for="toggle-'+ id +'"></label>' +
                '</div>' +
                '<input type="checkbox" id="toggle-'+ id +'" class="panel-image-toggle">' +
                '<div class="panel-body">' +
                '<h4 data-pk="'+ id +'" class="name"></h4>' +
                '<p data-pk="'+ id +'" class="description"></p>' +
                '</div>' +
                '<div class="panel-footer text-center">' +
                '<a href="'+ file +'" class="lightbox" rel="gallery"><span class="glyphicon glyphicon-zoom-in"></span></a>' +
                '<a href="'+ delete_ul +'" class="trash"><span class="glyphicon glyphicon-trash"></span></a>' +
                '<a href="#" class="move"><span class="glyphicon glyphicon-move"></span></a>' +
                '</div>' +
                '</div>' +
                '</div>';
            console.log(template);
            $(".gallery-images").append(template);
        }

    }, $.gallery);
})(jQuery, this);
