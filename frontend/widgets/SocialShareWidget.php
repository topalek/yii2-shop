<?php
/**
 * Created by PhpStorm.
 * User: Yatskanich Oleksandr
 * Date: 19.06.14
 * Time: 16:49
 */

namespace frontend\widgets;

use yii\base\Widget;

class SocialShareWidget extends Widget
{
    public $title,
        $desc,
        $imgUrl,
        $imgWidth = 300,
        $imgHeight = 400,
        $absoluteUrlForImg = true;

    public function run()
    {
        return $this->render(
            'social_share',
            [
                'title'             => $this->title,
                'desc'              => $this->desc,
                'imgUrl'            => $this->imgUrl,
                'imgWidth'          => $this->imgWidth,
                'imgHeight'         => $this->imgHeight,
                'absoluteUrlForImg' => $this->absoluteUrlForImg,
            ]
        );
    }
}
