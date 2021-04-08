<?php
/**
 * @link      https://github.com/himiklab/yii2-search-component-v2
 * @copyright Copyright (c) 2014 HimikLab
 * @license   http://opensource.org/licenses/MIT MIT
 */

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var ZendSearch\Lucene\Search\QueryHit[] $hits */
/** @var string $query */
/** @var yii\data\Pagination $pagination */

$query = yii\helpers\Html::encode($query);

$this->title = Yii::t('search', 'Результати пошуку по "{q}"', ['q' => $query]);
$this->params['breadcrumbs'] = [Yii::t('search', 'Пошук'), $this->title];

common\modules\search\SearchAssets::register($this);
$this->registerJs("jQuery('.search-item').highlight('{$query}');");

$ml_title = 'title_' . Yii::$app->language;
$ml_content = 'content_' . Yii::$app->language;
?>
<div class="search-index">
    <div class="content-block">
        <div class="block-title clearfix">
            <?php
            if (empty($hits)) {
                echo Html::tag('h3', Yii::t('search', 'По запиту "{q}" нічого не знайдено', ['q' => $query]));
            } else {
                echo Html::tag(
                    'h3',
                    Yii::t('search', 'Результати по запиту "{q}"', ['q' => $query]),
                    ['class' => 'pull-left']
                );
                echo Html::tag(
                    'span',
                    Yii::t('search', 'всього: {total}', ['total' => $total]),
                    ['class' => 'pull-right total-count']
                );
            }
            ?>
        </div>
    </div>
    <?php
    if (!empty($hits)): echo Html::beginTag('div', ['class' => 'list-view']); ?>
        <?php
        foreach ($hits as $hit): $url = \yii\helpers\Url::to($hit->url); ?>
            <div class="search-item media">
                <div class="media-left">
                    <?= Html::a($hit->img, $url) ?>
                </div>
                <div class="media-body">
                    <h4 class="media-heading"><?= Html::a($hit->$ml_title, $url) ?></h4>
                    <p><?= getShortText($hit->$ml_content, 500, true) ?></p>
                </div>
            </div>
        <?php
        endforeach; ?>
        <?= yii\widgets\LinkPager::widget(
            [
                'pagination' => $pagination,
            ]
        );
        ?>
        <?php
        echo Html::endTag('div'); endif; ?>
</div>
