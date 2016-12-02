<?php

namespace app\controllers;

use yii\base\UserException;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use app\models\Socio;
use app\models\ImportarSociosForm;
use \yii\helpers\Html;

class SocioController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => 
            [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => false,
                        'verbs' => ['GET'],
                        'actions' => ['borrar']
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],            
            ]
        ];
    }

	private function comprobar($id)
	{
		$socio = Socio::findOne($id);
		
		if (!$socio) {
			throw new UserException(\Yii::t('app', 'Ese socio no existe'));
		}	

		return $socio;
	}

    private function formulario($socio)
    {
        $esNuevo = $socio->id == null;
        $contrasenaAntigua = $socio->password;

        if ($socio->load(\Yii::$app->request->post())){

            $transaction = Socio::getDb()->beginTransaction();
            try {

                if (!strlen(trim($socio->password))) {
                    $socio->password = $contrasenaAntigua;
                } else {
                    $socio->password =  \Yii::$app->passwordHash->create_hash($socio->password);
                }

                if (!strlen(trim($socio->nombreUsuario))) {
                    $socio->nombreUsuario = null;
                }

                if (!strlen(trim($socio->email))) {
                    $socio->email = null;
                }
                
                if ($socio->save()) {

                    $transaction->commit();
                    \Yii::$app->session->setFlash('ok', \Yii::t('app', 'El socio se ha guardado correctamente'));

                    return $this->redirect(['editar', 'id' => $socio->id]);

                } else {
                    \Yii::$app->session->setFlash('error', \Yii::t('app', 'No se ha podido guardar el socio'));
                    $transaction->rollBack();    
                }

            } catch(\Exception $e) {
                \Yii::$app->session->setFlash('error', sprintf( \Yii::t('app', 'No se ha podido guardar el socio: %s'), $e->getMessage() ));
                $transaction->rollBack();
            }
        }

        if ($socio->id == null) {
            $socio->id = Socio::siguienteId();
        }

        $socio->password = '';
        return $this->render('editar', ['socio' => $socio ]);
    }

    public function actionEditar($id)
    {
        $socio = $this->comprobar($id);
        
        return $this->formulario($socio);
    }

    public function actionNuevo()
    {
        $socio = new Socio;

        return $this->formulario($socio);
    }

    public function actionActualizar($id = null)
    {
        $resultado = [];

        if ($id) {
            $socio = $socio = Socio::findOne($id);
            if (!$socio) {
                // El socio con ese id todavía no se ha creado
                $socio = new Socio;
                $socio->id = $id;
            }
        } else {
            $socio = new Socio;
            $socio->id = Socio::siguienteId();            
        }

        if ($socio->load(\Yii::$app->request->post())){
            $transaction = Socio::getDb()->beginTransaction();
            try {
                $esNuevo = $socio->fechaAlta == null;

                if ($socio->save()) {

                    if ($socio->esCorrectaLetraDni()) {
                        $estado = 'ok';

                        if ($esNuevo) {
                            $mensaje = sprintf(\Yii::t('app', 'Socio %d creado'), $socio->id);
                        } else {
                            $mensaje = sprintf(\Yii::t('app', 'Socio %d actualizado'), $socio->id);
                        }
                    } else {
                        $estado = 'warning';
                        $letra = $socio->letraDniCorrecta;

                        if ($esNuevo) {
                            $mensaje = sprintf(\Yii::t('app', 'Socio %d creado pero la letra del DNI debería ser la %s'), $socio->id, $letra);
                        } else {
                            $mensaje = sprintf(\Yii::t('app', 'Socio %d actualizado pero la letra del DNI debería ser la %s'), $socio->id, $letra);
                        }
                    }

                    $resultado = [
                        'estado' => $estado,
                        'mensaje' => $mensaje,
                        'id' => $socio->id
                    ];
                    $transaction->commit();
                } else {
                    $errores = $socio->getErrors();
                    $error = array_shift($errores);

                    $resultado = [
                        'estado' => 'error',
                        'mensaje' => sprintf( \Yii::t('app', 'Error: %s'), $error[0]),
                    ];
                    $transaction->rollBack();
                }
            } catch(\Exception $e) {         
                $resultado = [
                    'estado' => 'error',
                    'mensaje' => \Yii::t('app', 'Error en el servidor') . ': ' . $e->getMessage()
                ];
                $transaction->rollBack();
            }
        }

        echo json_encode($resultado);
    }

    public function actionGuardar($id)
    {
        $socio = $this->comprobar($id);

        return $this->formulario($socio);   
    }

    public function actionBorrar($id)
    {
        $socio = $this->comprobar($id);

        $transaction = Socio::getDb()->beginTransaction();

        try {

            $hayErrores = false;
            
            if ($socio->delete()) {
                \Yii::$app->session->setFlash('ok', \Yii::t('app', 'El socio se ha borrado correctamente'));
                $transaction->commit();
            } else {
                $hayErrores = true;
            }

            if ($hayErrores) {
                \Yii::$app->session->setFlash('error', \Yii::t('app', 'El socio no se ha podido borrar'));
                $transaction->rollBack();
            }

        } catch(\Exception $e) {
            \Yii::$app->session->setFlash('error', \Yii::t('app', 'El socio no se ha podido borrar'));
            $transaction->rollBack();
        }

        return $this->redirect(['index']);
    }

    public function actionIndex()
    {
    	$socios = [];

        return $this->render('index', ['socios' => $socios]);
    }

    public function actionImportar()
    {
        $importacion = new ImportarSociosForm();

        if (\Yii::$app->request->isPost) {
            $importacion->fichero = UploadedFile::getInstance($importacion, 'fichero');
            if ($importacion->subir()) {

                $datos = \moonland\phpexcel\Excel::import($importacion->ruta, [
                    'setFirstRecordAsKeys' => true,
                ]);

                \Yii::$app->session->setFlash('ok', \Yii::t('app', 'Los socios se han importado correctamente'));
                
                return $this->redirect(['index']);
            }
        }

        return $this->render('importar', ['importacion' => $importacion]);
    }

    public function actionExportar()
    {
        $socios = Socio::find()->all();

        // \moonland\phpexcel\Excel::export([
        //     'models' => $socios, 
        //     'columns' => ['nombre', 'apellidos']
        // ]);
    }

    public function actionMultiple()
    {
        return $this->render('multiple');
    }

    public function actionErrores()
    {
        $errores = [];

        $socios = Socio::find()->all();

        if ($socios && count($socios)) {
            $dnis = [];
            foreach ($socios as $socio) {

                $erroresEnSocio = $socio->erroresEnDatos();
                if (count($erroresEnSocio)) {
                    switch ($erroresEnSocio[0]) {
                        case Socio::ERROR_NO_NOMBRE:
                            $errores[] = [
                                'campo' => \Yii::t('app', 'Nombre'),
                                'mensaje' => \Yii::t('app', '{socio} no tiene <b>nombre</b>', ['socio' => Html::a($socio->nombreCompleto, ['/socio/editar', 'id' => $socio->id])])
                            ];
                            break;
                        case Socio::ERROR_NO_APELLIDOS:
                            $errores[] = [
                                'campo' => \Yii::t('app', 'Apellidos'),
                                'mensaje' => \Yii::t('app', '{socio} no tiene <b>apellidos</b>', ['socio' => Html::a($socio->nombreCompleto, ['/socio/editar', 'id' => $socio->id])])
                            ];
                            break;
                        case Socio::ERROR_NO_DNI:
                            $errores[] = [
                                'campo' => \Yii::t('app', 'DNI'),
                                'mensaje' => \Yii::t('app', '{socio} no tiene <b>DNI</b>', ['socio' => Html::a($socio->nombreCompleto, ['/socio/editar', 'id' => $socio->id])])
                            ];
                            break;
                        case Socio::ERROR_NO_LETRA_DNI:
                            $errores[] = [
                                'campo' => \Yii::t('app', 'DNI'),
                                'mensaje' => \Yii::t('app', 'El <b>DNI</b> de {socio} no tiene letra', ['socio' => Html::a($socio->nombreCompleto, ['/socio/editar', 'id' => $socio->id])])
                            ];
                            break;
                        case Socio::ERROR_NO_DIGITO_DNI:
                            $errores[] = [
                                'campo' => \Yii::t('app', 'DNI'),
                                'mensaje' => \Yii::t('app', 'Falta algún dígito en el <b>DNI</b> de {socio}', ['socio' => Html::a($socio->nombreCompleto, ['/socio/editar', 'id' => $socio->id])])
                            ];
                            break;
                        case Socio::ERROR_MAS_DIGITO_DNI:
                            $errores[] = [
                                'campo' => \Yii::t('app', 'DNI'),
                                'mensaje' => \Yii::t('app', 'Sobra algún dígito en el <b>DNI</b> de {socio}', ['socio' => Html::a($socio->nombreCompleto, ['/socio/editar', 'id' => $socio->id])])
                            ];
                            break;
                        case Socio::ERROR_LETRA_DNI_INCORRECTA:
                            $errores[] = [
                                'campo' => \Yii::t('app', 'DNI'),
                                'mensaje' => \Yii::t('app', 'La letra del <b>DNI</b> de {socio} no es correcta', ['socio' => Html::a($socio->nombreCompleto, ['/socio/editar', 'id' => $socio->id])])
                            ];
                            break;
                    }
                } else {
                    $dniSinLetra = substr($socio->dni, 0, 8);

                    if (isset($dnis[$dniSinLetra])) {
                        $socio1 = $dnis[$dniSinLetra];

                        $error = [
                            'campo' => \Yii::t('app', 'DNI'),
                            'mensaje' => \Yii::t('app', '{socio1} y {socio2} tienen el mismo <b>DNI</b>', [
                                'socio1' => Html::a($socio1->nombreCompleto, ['/socio/editar', 'id' => $socio1->id]),
                                'socio2' => Html::a($socio->nombreCompleto, ['/socio/editar', 'id' => $socio->id])
                            ])
                        ];
                        $errores[] = $error;
                    } else {
                        $dnis[$dniSinLetra] = $socio;
                    }
                }
            }
        }

        return $this->render('errores', ['errores' => $errores]);
    }

    public function actionApi()
    {
        $post = \Yii::$app->request->post();
        
        $datos = Socio::dataTable($post['start'], $post['length'], $post['search']['value'], $post['order']);
        
        $resultado = [];
        $resultado['draw'] = $post['draw'];
        $resultado['recordsTotal'] = $datos['totales']['recordsTotal'];
        $resultado['recordsFiltered'] = $datos['totales']['recordsFiltered'];
        $resultado['data'] = [];

        foreach ($datos['socios'] as $socio) {
            $s = [];

            $s[] = $socio->id;
            $s[] = $socio->nombreCompleto;
            $s[] = $socio->dni;
            $s[] = '<span class="text-yellow">' . $socio->primerError . '</span>';

            $acciones = '';
            $acciones .= $this->renderPartial('/site/_borrar', [
                'accion' => ['socio/borrar', 'id' => $socio->id],
                'mensajeConfirmacion' => \Yii::t('app', '¿Estás seguro de que quieres borrar este socio?')
              ]);
            $acciones .= Html::a('Editar', ['socio/editar', 'id' => $socio->id], ['class' => 'pull-right']);

            $s[] = $acciones;

            $resultado['data'][] = $s;
        }

        echo json_encode($resultado);
    }

    public function actionApiDropdown($term = null)
    {
        $resultado = [];
        $resultado['results'] = [];
        $resultado['more'] = false;

        if ($term) {
            $socios = Socio::find()->where(
                ['or',
                    ['or',
                        ['like', 'nombre', $term],
                        ['like', 'apellidos', $term]
                    ],
                    ['like', 'dni', $term]
                ]
            )->all();

            foreach ($socios as $socio) {
                $s = [];
                $s['id'] = $socio->id;
                $s['text'] = $socio->resumen;

                $resultado['results'][] = $s;
            }
        }

        echo json_encode($resultado);
    }

}
