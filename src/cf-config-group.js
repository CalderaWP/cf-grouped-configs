function CF_Processor_Group_Field( $, slug ) {

	//alias this as self for use inside of functions
	var self = this;

	//holds processor ID
	this.pId = '';

	//used to track if remove code button has been hidden
	this.removeHidden = false;

	// trigger group remove
	$( document ).on( 'click', '.' + slug + '-group-remove', function () {
		var clicked = $(this),
			confirm_txt = clicked.data('confirm'),
			wrapper = $( this ).parent();

		var group_count = self.count_groups();

		if(  group_count <= 2 ){
			self.removeHidden = true;
			$( '.' + slug + '-group-remove' ).hide().attr( 'aria-hidden', true ).css( 'visibility', 'none' );
		}else{
			if( true === self.removeHidden ){
				$( '.' + slug + '-group-remove' ).show().attr( 'aria-hidden', false ).css( 'visibility', 'visible' );
			}

		}

		if ( confirm( confirm_txt )) {
			wrapper.remove();
		}


	});

	//maybe unhide remove group button when adding new button
	$( document ).on( 'click', '.' + slug + '-group-add', function () {
		if(  self.count_groups != 1 && true === self.removeHidden ){
			$( '.' + slug + '-group-remove' ).show().attr( 'aria-hidden', false ).css( 'visibility', 'visible' );
		}
	});

	this.count_groups = function(){
		return $( '#' + self.pId + '_groups .' + slug + '-group' ).length;
	};

	// add trigger to build groups
	$(document).on('click', '.processor_type_cf-discount', function () {
		var clicked = $(this),
			pid = $('#' + clicked.find('input').val() + '_config_groups');

		if (pid.length) {
			pid.trigger('build_groups');
		}
	});

	// build trigger
	this.group = function (obj) {
		var id = 'cfd' + Math.round(Math.random() * 18746582734), // generate a random ID
			name = obj.trigger.data('name'),
			groups = obj.trigger.val();

		self.pId = obj.trigger.data( 'processor-id' );


		// can we json this thing?
		if (groups.length) {

			groups = JSON.parse(groups);
			config = {
				group: groups
			};

			for (var g in config.group) {
				config.group[g]._id = g;
				config.group[g]._name = name + '[group][' + g + ']';
			}


		} else {
			config = {
				group: {
					id: {
						_id: id,
						_name: name + '[group][' + id + ']'
					}
				}
			};
		}

		return config;
	};

	// get rid of the first remove code button to keep at least a single group
	this.cleanup = function (obj) {
		var first_remover = obj.params.target.find( '.' + self.prefix ).first().find( '.' + self.prefix + '-group-remove');
		if (first_remover.length) {
			first_remover.remove();
		}

	};



}
