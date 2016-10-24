/* global $ */
$(function() {

	$('.icheck').iCheck({
		checkboxClass: 'icheckbox_square-blue',
		radioClass: 'iradio_square-blue',
		increaseArea: '20%' // optional
	});

	if ($('.js-mensaje-flash').length) {
		setTimeout(function() {
			$('.js-mensaje-flash').fadeOut();
		}, 3000);
	}

	$('.js-dataTable').each(function() {
		var $this = $(this);
		var tabla;
		var opciones = {
			paging: true,
			pagingType: 'simple',
			lengthChange: false,
			searching: true,
			ordering: true,
			info: true,
			autoWidth: false,
			language: {
				url: '/js/lang/dataTable.js'
			}
		};
		var columnaPorDefecto = $this.find('th.js-dataTable-ordenar').index();

		if (columnaPorDefecto >= 0) {
			opciones.order = [[columnaPorDefecto, 'asc']];
		}

		if ($this.attr('data-url')) {
			opciones.serverSide = true;
			opciones.ajax = {
				url: $this.attr('data-url'),
				type: 'POST'
			};
		}

		tabla = $this.DataTable(opciones);

	});

	$('.js-select2').each(function() {
		var $this = $(this);
		var opciones = {};

		if ($this.attr('data-url')) {
			opciones.ajax = {
				url: $this.attr('data-url'),
				dataType: 'json'
			};
		}

		$this.select2(opciones);
	});
});