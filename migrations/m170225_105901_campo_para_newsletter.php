<?php

use yii\db\Migration;

class m170225_105901_campo_para_newsletter extends Migration
{
    public function safeUp()
    {
        $this->addColumn('socio', 'quiereNewsletter', $this->boolean()->notNull()->defaultValue(false));
    }

    public function safeDown()
    {
        $this->dropColumn('socio', 'quiereNewsletter');
    }
}
