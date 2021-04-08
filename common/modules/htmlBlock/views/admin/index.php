<?php

use yii\grid\GridView;
use yii\helpers\Html;

/**
 * @var yii\web\View                                    $this
 * @var common\modules\htmlBlock\models\HtmlBlockSearch $searchModel
 * @var yii\data\ActiveDataProvider                     $dataProvider
 */

$this->title = 'Html блоки';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="html-block-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php
    // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Создать блок', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget(
        [
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'columns'      => [
                'id',
                'title',
                [
                    'attribute' => 'content',
                    'filter'    => false,
                    'value'     => function ($data) {
                        return getShortText($data->content, 450, true);
                    },
                ],
                [
                    'class'    => 'yii\grid\ActionColumn',
                    'template' => '{update} {delete}',
                ],
            ],
        ]
    ); ?>
</div>

<?php
$js = <<<JS
$('.toggle-action').click(function(){
    var item = $(this).parents('tr').attr('data-key');
    $.ajax({
        url: '/htmlBlock/admin/toggle-status',
        type: 'get',
        data: {id:item},
        success: function(result){
            humane.log(result,{timeout:2000});
            $.pjax.reload({container:'#htmlBlock-grid'});
        }
    });
});
JS;
$this->registerJs($js);
?>
