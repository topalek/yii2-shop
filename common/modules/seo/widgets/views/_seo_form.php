<?php
/**
 * @var $seo Seo
 */

use common\modules\seo\models\Seo;
use common\modules\translate\models\Translate;
use yii\helpers\Html;

?>
<div class="panel panel-primary">
    <div class="panel-heading" onclick="$(this).next().toggle('fast');" style="cursor: pointer">
        <h3 class="panel-title"><?= Yii::t('seo', 'SEO data') ?></h3>
    </div>
    <div class="panel-body" style="display: none">
        <dl class="tabs">
            <?php
            foreach (Translate::getLangList() as $lang => $langTitle) :?>
                <dt><?= $langTitle ?></dt>
                <dd>
                    <div class="form-group  col-md-12">
                        <?= Html::activeLabel($seo, 'title_' . $lang, ['class' => 'control-label']) ?>
                        <?= Html::activeTextInput($seo, 'title_' . $lang, ['class' => 'form-control']) ?>
                    </div>

                    <div class="form-group col-md-6">
                        <?= Html::activeLabel($seo, 'description_' . $lang, ['class' => 'control-label']) ?>
                        <?= Html::activeTextInput($seo, 'description_' . $lang, ['class' => 'form-control']) ?>
                    </div>

                    <div class="form-group col-md-6">
                        <?= Html::activeLabel($seo, 'keywords_' . $lang, ['class' => 'control-label']) ?>
                        <?= Html::activeTextInput($seo, 'keywords_' . $lang, ['class' => 'form-control']) ?>
                    </div>
                </dd>
            <?php
            endforeach; ?>
        </dl>

        <div class="form-group col-md-12">
            <?= Html::activeLabel($seo, 'external_link', ['class' => 'control-label']) ?>
            <div class="input-group">
                <span class="input-group-addon">http://<?= $_SERVER['HTTP_HOST'] ?>/</span>
                <?= Html::activeTextInput($seo, 'external_link', ['class' => 'form-control']) ?>
            </div>
        </div>

        <div class="form-group col-md-3">
            <?= Html::activeCheckbox($seo, 'noindex') ?>
        </div>

        <div class="form-group col-md-3">
            <?= Html::activeCheckbox($seo, 'nofollow') ?>
        </div>

        <div class="form-group col-md-3">
            <?= Html::activeCheckbox($seo, 'in_sitemap') ?>
        </div>

        <div class="form-group col-md-3">
            <?= Html::activeCheckbox($seo, 'is_canonical') ?>
        </div>

    </div>
</div>
