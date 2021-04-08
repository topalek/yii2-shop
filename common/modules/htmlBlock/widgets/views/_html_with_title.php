<?php
/**
 * Created by PhpStorm.
 * User: yurik
 * Date: 18.06.14
 * Time: 10:32
 *
 * @var $model HtmlBlock
 */

use common\modules\htmlBlock\models\HtmlBlock; ?>
<?php
if ($this->context->getAll): ?>
    <?php
    foreach ($models as $model) {
        echo \yii\helpers\Html::tag('div', $model->title);
        echo \yii\helpers\Html::tag('div', $model->getMlContent());
    }
    ?>
<?php
else: ?>
    <div>
        <?= $model->title ?>
    </div>

    <div>
        <?= $model->getMlContent() ?>
    </div>
<?php
endif; ?>
