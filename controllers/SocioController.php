<?php

namespace app\controllers;

use yii\base\UserException;
use yii\filters\AccessControl;
use app\models\Socio;
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

                if ($esNuevo) {
                    $socio->fechaAlta = \Yii::$app->formatter->asDatetime(new \DateTime);
                }

                if (!strlen(trim($socio->nombreUsuario))) {
                    $socio->nombreUsuario = null;
                }

                if (!strlen(trim($socio->email))) {
                    $socio->email = null;
                }
                
                if ($socio->save()) {

                    if ($esNuevo) {
                        $auth = \Yii::$app->authManager;
                        $rol = $auth->getRole('socio');
                        $auth->assign($rol, $socio->id);
                    }

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
            $auth = \Yii::$app->authManager;
            $auth->revokeAll($socio->id);

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

    public function actionExportar()
    {
        $socios = Socio::find()->all();

        // \moonland\phpexcel\Excel::export([
        //     'models' => $socios, 
        //     'columns' => ['nombre', 'apellidos']
        // ]);
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
