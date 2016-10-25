<?php

namespace app\models;

use Yii;

class ImportarSociosForm extends \yii\base\Model
{

    public $fichero;
    public $ruta;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fichero'], 'file', 'skipOnEmpty' => false, 'extensions' => 'xlsx', 'checkExtensionByMimeType' => false],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'fichero' => \Yii::t('app', 'Fichero Excel')                       
        ];
    }

    public function subir()
    {
        if ($this->validate()) {
            $hoy = new \DateTime;

            \yii\helpers\FileHelper::createDirectory(Yii::getAlias('@runtime/importaciones'));

            $ruta = Yii::getAlias('@runtime/importaciones/' . $hoy->format('Y-m-d-h-i-s') . '_' . $this->fichero->baseName . '.' . $this->fichero->extension);

            if ($this->fichero->saveAs($ruta)) {
                $this->ruta = $ruta;
                return true;    
            } else {
                return false;
            }            
            
        } else {
            return false;
        }
    }
}
