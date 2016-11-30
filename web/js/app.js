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
		var _nColumnas = _columnas.length;
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

			_atributos.push(columna.atributo);
			_colHeaders.push(columna.nombre);
			_columns.push(prop);
			_colWidths.push(ancho);
			_data.push(inicial);
		});
		
		var _mensajeGuardando = $this.data('mensaje-guardando');
		var _mensajeErrorServidor = $this.data('mensaje-error-servidor');

		var _ultimaFila = 0;
		var _hayCambios = false;

		var hot = new Handsontable(this, {
			data: [_data],
			minSpareRows: 1,
			autoWrapRow: true,
			stretchH: 'all',
			rowHeaders: true,
			colHeaders: _colHeaders,
			columns: _columns,
			colWidths: _colWidths,
			/*
			 * Formato de las celdas
			 */
			cells: function (row, col, prop) {
				var cellProperties = {};
				
				if (col == _columnaServidor) {
					cellProperties.renderer = 'mensajeDeServidorRenderer';
				}

				return cellProperties;
			},
			/*
			 * Eventos del usuario
			 */
			// Al movermos con el tabulador nos saltamos las columnas
			// que sean de solo lectura
			tabMoves: function(e){
				
				var seleccion = hot.getSelected();
				var fila = seleccion[0];
				var col = seleccion[1];
				var podemosEscribir = false;
				var delta = e.shiftKey ? -1 : 1;

				col = col + delta;
				
				// Buscamos la siguiente celda en la que podamos escribir
				while (!podemosEscribir) {
					
					if (fila < 0) {
						// Si no hay más filas nos quedamos en la misma celda
						fila = seleccion[0];
						col = seleccion[1];
						podemosEscribir = true;
					} else {
						if (col < 0) {
							// Si es la primera columna, saltamos a la fila anterior
							fila = fila - 1;
							col = _nColumnas - 1;
						} else {
							// Si es la última columna, saltamos a la fila siguiente
							if (col >= _nColumnas) {
								fila = fila + 1;
								col = 0;
							} else {
								// Comprobamos si se puede escribir en la celda
								siguenteCelda = hot.getCellMeta(fila, col);

								if (siguenteCelda.readOnly) {
									// Si no se puede, saltamos a la siguiente celda
									col = col + delta;
								} else {
									podemosEscribir = true;
								}
							}
						}
					}
				}
				
				return {row: delta * (fila - seleccion[0]), col: delta * (col - seleccion[1])};
			},
			// Detecta cuando ha habido cambios en una fila
			afterChange: function(cambios, tipo) {
				if (cambios && tipo == 'edit') {
					_hayCambios = true;
				}
			},
			afterSelection: function (filaInicial, colInicial, filaFinal, colFinal) {
				if (_columnaId !== null) {
					for (var i = filaInicial; i <= filaFinal; i++) {
						// Si todavía no tiene id, se lo asignamos
						var id = hot.getDataAtCell(i, _columnaId);
						if (!id) {
							this.setDataAtCell(i, _columnaId, _id++, 'loadData');
						}
					}
				}
			},
			// Cuando cambiamos de fila, envía los nuevos datos
			// al servidor y recibe su respuesta
			afterSelectionEnd: function(fila) {
				if (_hayCambios && _ultimaFila != fila) {
					var filaActual = _ultimaFila;
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

					_hayCambios = false;
				}
				
				_ultimaFila = fila;
			}
		});

	});
});