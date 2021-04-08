<?php
/**
 * Created by PhpStorm.
 * User: Yatskanich Oleksandr
 * Date: 08.10.14
 * Time: 12:50
 *
 * @var $this View
 */

use yii\web\View;

?>
<div id="uploader<?= $selector ?>" class="uploader">
    <div class="js-fileapi-wrapper">
        <input type="file" name="<?= $fileVar ?>"/>
    </div>
    <div data-fileapi="active.show" class="progress">
        <div data-fileapi="progress" class="progress__bar"></div>
    </div>
    <?= $input ?>
</div>

