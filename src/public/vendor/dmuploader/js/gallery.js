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

		addFileUploaded : function(id, file, delete_ul, locales){
			var template = '<div class="col-xs-6 col-md-3 photo-item sortable" id="photo_'+ id +'">' +
								'<div class="panel panel-default panel-photo">' +
									'<div class="panel-image">' +
										'<figure class="gallery-photo">' +
											'<img src="'+ file +'" class="panel-image-preview " />' +
											'<figcaption>' +
												'<ul class="list-unstyled image-tools">' +
													'<li><a href="'+ file +'" class="lightbox" rel="gallery"><span class="glyphicon glyphicon-zoom-in fs-24"></span></a></li>' +
													'<li><a href="'+ delete_ul +'" data-photoid="'+ id + '" class="trash"><span class="glyphicon glyphicon-trash fs-24"></span></a></li>' +
													'<li><a href="#" class="move"><span class="glyphicon glyphicon-move fs-24"></span></a></li>' +
												'</ul>' +
											'</figcaption>' +
										'</figure>' +
										'<label for="toggle-'+ id +'"></label>' +
									'</div>' +
									'<input type="checkbox" id="toggle-'+ id +'" class="panel-image-toggle hidden">' +
									'<div class="panel-body tab-block">' +
										'<ul class="nav nav-tabs nav-justified">';
											for (index = 0; index < locales.length; ++index) {
												var active = (currentLocale == locales[index]["iso"]) ? "active" : "";
				template += 				'<li class="'+ active  +'">' +
												'<a data-toggle="tab" href="#'+locales[index]["iso"]+'-'+id+'">'+locales[index]["language"]+'</a>' +
											'</li>';
											}
				template +=				'</ul>' +
										'<div class="tab-content">';
											for (index = 0; index < locales.length; ++index) {
												var active = (currentLocale == locales[index]["iso"]) ? "active" : "";
				template +=					'<div class="tab-pane '+ active + '" id="'+locales[index]["iso"]+'-'+id+'">' +
												'<h4 data-pk="'+ id +'" data-lang="'+locales[index]["iso"]+'" class="name"></h4>' +
												'<p data-pk="'+ id +'" data-lang="'+locales[index]["iso"]+'" class="description"></p>' +
											'</div>';
											}
				template += 		'</div>' +
								'</div>' +
							'</div>';
			console.log(template);
			$(".gallery-images").append(template);
		}

	}, $.gallery);
})(jQuery, this);

