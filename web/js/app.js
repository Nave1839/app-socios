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

			switch(cellProperties.estado) {
				case 'ok':
					clase = 'text-green';
					break;
				case 'warning':
					clase = 'text-yellow';
					break;
				case 'error':
					clase = 'text-red';
					break;
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
		// Columna que guarda la id de la entidad
		var _columnaId = null;
		var _id = 1;
		// Columna que muestra los mensajes del servidor
		var _columnaServidor;

		// Traducimos los parámetros de configuración de la tabla
		var _colHeaders = [];
		var _columns = [];
		var _colWidths = [];
		var _data = [];
		
		$.each(_columnas, function(i, columna) {
			var prop = {};
			var ancho = 150;
			var inicial = '';

			switch (columna.tipo) {
				case 'id':
					_columnaId = i;
					_id = columna.inicial;
					prop.readOnly = true;
					inicial = _id++;
					break;
				case 'servidor':
					_columnaServidor = i;
					prop.readOnly = true;
					break;
			}

			if (!!columna.ancho) {
				ancho = columna.ancho;
			}

			_colHeaders.push(columna.nombre);
			_columns.push(prop);
			_colWidths.push(ancho);
			_data.push(inicial);
		});
		
		var _atributos = $.map(_columnas, function (col, i) {
			return col.atributo;
		});

		var _mensajeGuardando = $this.data('mensaje-guardando');
		var _mensajeErrorServidor = $this.data('mensaje-error-servidor');

		var ultimaFila = 0;
		var hayCambios = false;

		var hot = new Handsontable(this, {
			data: [_data],
			minSpareRows: 1,
			autoWrapRow: true,
			stretchH: 'all',
			rowHeaders: true,
			colHeaders: _colHeaders,
			columns: _columns,
			colWidths: _colWidths,
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
					if (_columnaId !== null) {
						// Si todavía no tiene id, se lo asignamos
						var id = hot.getDataAtCell(ultimaFila, _columnaId);
						if (!id) {
							this.setDataAtCell(ultimaFila, _columnaId, _id++, 'loadData');
						}
					}
				}
			},
			// Cuando cambiamos de fila, envía los nuevos datos
			// al servidor y recibe su respuesta
			afterSelectionEnd: function(fila) {
				if (hayCambios && ultimaFila != fila) {
					var filaActual = ultimaFila;
					var url = _url;
					var datos = {};
					var hayMensajesAServidor = !!_columnaServidor;
					var id = _columnaId !== null ? hot.getDataAtCell(filaActual, _columnaId) : null;

					if (hayMensajesAServidor) {
						hot.setCellMeta(filaActual, _columnaServidor, 'estado', '');
						hot.setDataAtCell(filaActual, _columnaServidor, _mensajeGuardando, 'loadData');
					}

					// Si la fila está asociada a una entidad,
					// actualizamos la url
					if (id) {
						url = url + '/' + id;
					}

					// Guardamos los datos a enviar, asociándolos
					// con los nombres de los atributos
					$.each(_atributos, function(i, nombre) {
						var valor = hot.getDataAtCell(filaActual, i);
						if (!!nombre && !!valor) {
							datos[nombre] = valor;
						}
					});

					$.post(url,
						datos,
						function(resultado) {
							if (hayMensajesAServidor) {
								hot.setCellMeta(filaActual, _columnaServidor, 'estado', resultado.estado);
								if (resultado.id) {
									hot.setCellMeta(filaActual, _columnaServidor, 'id', resultado.id);
								}
								hot.setDataAtCell(filaActual, _columnaServidor, resultado.mensaje, 'loadData');
							}
						},
						'json'
					).fail(function () {
						// Error de servidor
						if (hayMensajesAServidor) {
							hot.setCellMeta(filaActual, _columnaServidor, 'estado', 'error');
							hot.setDataAtCell(filaActual, _columnaServidor, _mensajeErrorServidor, 'loadData');
						}
					});

					hayCambios = false;
				}
				
				ultimaFila = fila;
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