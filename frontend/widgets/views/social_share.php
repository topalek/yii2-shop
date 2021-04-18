<?php
/**
 * @var $this              \yii\web\View
 * @var $title             string
 * @var $desc              string
 * @var $imgUrl            string
 * @var $absoluteUrlForImg bool
 * @var $imgWidth          integer
 * @var $imgHeight         integer
 */

use yii\helpers\Html;

$pageAbsoluteUrl = Yii::$app->request->absoluteUrl;

$this->registerMetaTag(['property' => 'og:site_name', 'content' => Yii::$app->name]);
$this->registerMetaTag(['property' => 'og:locale', 'content' => Yii::$app->language . '_ru']);
$this->registerMetaTag(['property' => 'og:title', 'content' => $title]);
$this->registerMetaTag(['property' => 'og:type', 'content' => 'product']);
$this->registerMetaTag(['property' => 'og:url', 'content' => $pageAbsoluteUrl]);
$this->registerMetaTag(['property' => 'og:description', 'content' => strip_tags($desc)]);
$this->registerMetaTag(['property' => 'article:section', 'content' => 'product']);
if ($imgUrl) {
    if ($absoluteUrlForImg) {
        $imgUrl = Yii::$app->urlManager->createAbsoluteUrl($imgUrl, null, false);
    }
    $this->registerMetaTag(['property' => 'og:image', 'content' => $imgUrl]);
    $this->registerMetaTag(['property' => 'og:image:width', 'content' => $imgWidth]);
    $this->registerMetaTag(['property' => 'og:image:height', 'content' => $imgHeight]);
    $this->registerMetaTag(['itemprop' => 'image', 'content' => $imgUrl]);
}

$shareText = Yii::t('social', 'Поделиться');
$shareUrl = urldecode($pageAbsoluteUrl);
?>

    <div class="social-share-block">
        <h5><?= Yii::t('social', 'Поделиться в социальных сетях') ?></h5>
        <ul>
            <li>
                <?= Html::a(
                    '<i class="fa fa-facebook"></i>',
                    'javascript:(0)',
                    [
                        'title'   => Yii::t('social', 'Поделиться в {network}', ['network' => 'Facebook']),
                        'target'  => '_blank',
                        'onclick' => "shareFb('$shareUrl'); return false;",
                    ]
                ) ?>
            </li>
            <li>
                <?= Html::a(
                    '<i class="fa fa-google"></i>',
                    'javascript:(0)',
                    [
                        'title'   => Yii::t('social', 'Поделиться в {network}', ['network' => 'Google']),
                        'target'  => '_blank',
                        'onclick' => "shareGoogle('$shareUrl'); return false;",
                    ]
                ) ?>
            </li>
            <li>
                <?= Html::a(
                    '<i class="fa fa-twitter"></i>',
                    'javascript:(0)',
                    [
                        'title'   => Yii::t('social', 'Поделиться в {network}', ['network' => 'Twitter']),
                        'target'  => '_blank',
                        'onclick' => "shareTwitter('$shareUrl'); return false;",
                    ]
                ) ?>
            </li>
        </ul>
    </div>


<?php
$this->registerJs(
    <<<JS
function shareFb(url) {
    window.open('https://www.facebook.com/sharer/sharer.php?u=' + url, 'facebook-share-dialog', "width=626,height=436")
}
function shareGoogle(url) {
    window.open('https://plus.google.com/share?url=' + url, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');
}
function shareTwitter(url) {
    window.open('https://twitter.com/share?url=' + url, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');
}
JS
    ,
    $this::POS_END
);






