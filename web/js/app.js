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

	/*
	 * Hansontables
	 */

	// Colorea las celdas de la columna con los mensajes del servidor, 
	// en función de si se trata de una operación correcta o un error
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

		var $this = $(this);
		// Los metadatos de la hoja se guardan como atributos HTML del contenedor
		var _columnas = $this.data('columnas');
		// Url en la que se guardan los datos de la hoja
		var _url = $this.data('url');
		// Columna que muestra los mensajes del servidor
		var _columnaServidor = parseInt($this.data('columna-servidor'), 10);

		var _atributos = $.map(_columnas, function (col, i) {
			return col.atributo;
		});

		var _mensajeGuardando = $this.data('mensaje-guardando');
		var _mensajeErrorServidor = $this.data('mensaje-error-servidor');

		var ultimaFila = 0;
		var hayCambios = false;

		var hot = new Handsontable(this, {
			minSpareRows: 1,
			rowHeaders: true,
			colHeaders: $.map(_columnas, function (col, i) {
				return col.nombre;
			}),
			columns: $.map(_columnas, function (col, i) {
				var prop = {};

				if (i == _columnaServidor) {
					prop.readOnly = true;
				}

				return prop;
			}),
			autoWrapRow: true,
			colWidths: 200,
			// Al movermos con el tabulador nos saltamos las columnas
			// que sean de solo lectura
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
			// Detecta cuando ha habido cambios en una fila
			afterChange: function(cambios, tipo) {
				if (cambios && tipo == 'edit') {
					hayCambios = true;
				}
			},
			// Cuando cambiamos de fila, envía los nuevos datos
			// al servidor y recibe su respuesta
			afterSelectionEnd: function(fila) {
				if (hayCambios && ultimaFila != fila) {
					var filaActual = ultimaFila;
					var meta = hot.getCellMeta(filaActual, _columnaServidor);
					var url = _url;
					var datos = {};

					hot.setCellMeta(filaActual, _columnaServidor, 'estado', '');
					hot.setDataAtCell(filaActual, _columnaServidor, _mensajeGuardando, 'loadData');

					// Si la fila está asociada a una entidad,
					// actualizamos la url
					if (meta.id) {
						url = url + '/' + meta.id;
					}

					// Guardamos los datos a enviar, asociándolos
					// con los nombres de los atributos
					$.each(_atributos, function(i, valor) {
						if (!!valor) {
							datos[valor] = hot.getDataAtCell(filaActual, i);
						}
					});

					$.post(url,
						datos,
						function(resultado) {
							hot.setCellMeta(filaActual, _columnaServidor, 'estado', resultado.estado);
							if (resultado.id) {
								hot.setCellMeta(filaActual, _columnaServidor, 'id', resultado.id);
							}
							hot.setDataAtCell(filaActual, _columnaServidor, resultado.mensaje, 'loadData');
						},
						'json'
					).fail(function () {
						// Error de servidor
						hot.setCellMeta(filaActual, _columnaServidor, 'estado', 'error');
						hot.setDataAtCell(filaActual, _columnaServidor, _mensajeErrorServidor, 'loadData');
					});
				}
				
				ultimaFila = fila;
				hayCambios = false;
			},
			cells: function (row, col, prop) {
				var cellProperties = {};
				
				if (col == _columnaServidor) {
					cellProperties.renderer = 'mensajeDeServidorRenderer';
				}

				return cellProperties;
			}
		});
	});
});