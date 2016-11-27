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

	function mensajeDeServidorRenderer(instance, td, row, col, prop, value, cellProperties) {
		Handsontable.renderers.TextRenderer.apply(this, arguments);

		var clase = 'text-muted';

		if (cellProperties) {
			if (cellProperties.estado == 'ok') {
				clase = 'text-green';
			} else {
				if (cellProperties.estado == 'error') {
					clase = 'text-red';
				}
			}
		}

		td.className = clase;
	}
	Handsontable.renderers.registerRenderer('mensajeDeServidorRenderer', mensajeDeServidorRenderer);


	$('.js-handsontable').each(function() {

		var data = [
			['','','','']
		];

		var ultimaFila = 0;
		var hayCambios = false;

		var hot = new Handsontable(this, {
			data: data,
			minSpareRows: 1,
			rowHeaders: true,
			colHeaders: [
				'Nome',
				'Apelidos',
				'DNI',
				'Mensaxe'
			],
			columns: [
				{},
				{},
				{},
				{
					readOnly: true
				}
			],
			autoWrapRow: true,
			colWidths: 200,
			tabMoves: function(e){
				
				var seleccion = hot.getSelected();
				
				var siguenteCelda;
				
				if(e.shiftKey){
					siguenteCelda = hot.getCellMeta(seleccion[0], seleccion[1] - 1);
				} else {
					siguenteCelda = hot.getCellMeta(seleccion[0], seleccion[1] + 1);
				}
				
				var filaDelta = 0;
				var colDelta = siguenteCelda.readOnly ? 2 : 1;
				
				return {row: filaDelta, col: colDelta};
			},
			afterChange: function(cambios, tipo) {
				if (cambios && tipo == 'edit') {
					hayCambios = true;
				}
			},
			afterSelectionEnd: function(fila) {
				if (hayCambios && ultimaFila != fila) {
					var filaActual = ultimaFila;
					var meta = hot.getCellMeta(filaActual, 3);
					var url = '/socio/actualizar';

					hot.setCellMeta(filaActual, 3, 'estado', '');
					hot.setDataAtCell(filaActual, 3, 'Guardando...', 'loadData');

					console.log(meta);

					if (meta.id) {
						url = url + '/' + meta.id;
					}

					$.post(url,
						{
							'Socio[nombre]': hot.getDataAtCell(filaActual, 0),
							'Socio[apellidos]':hot.getDataAtCell(filaActual, 1),
							'Socio[dni]': hot.getDataAtCell(filaActual, 2),
						},
						function(resultado) {
							hot.setCellMeta(filaActual, 3, 'estado', resultado.estado);
							if (resultado.id) {
								hot.setCellMeta(filaActual, 3, 'id', resultado.id);
							}
							hot.setDataAtCell(filaActual, 3, resultado.mensaje, 'loadData');
						},
						'json'
					).fail(function () {
						hot.setCellMeta(filaActual, 3, 'estado', 'error');
						hot.setDataAtCell(filaActual, 3, 'Error en el servidor', 'loadData');
					});
				}
				
				ultimaFila = fila;
				hayCambios = false;
			},
			cells: function (row, col, prop) {
				var cellProperties = {};
				
				if (col == 3) {
					cellProperties.renderer = 'mensajeDeServidorRenderer';
				}

				return cellProperties;
			}
		});
	});
});