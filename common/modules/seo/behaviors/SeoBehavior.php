<?php
/**
 * Created by PhpStorm.
 * User: yurik
 * Date: 27.05.14
 * Time: 15:47
 */

namespace common\modules\seo\behaviors;

use common\modules\seo\models\Seo;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\Inflector;

class SeoBehavior extends Behavior
{

    public $model;
    public $view_category;
    public $view_action;

    /**
     * @return array
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT  => 'writeSeo',
            ActiveRecord::EVENT_AFTER_UPDATE  => 'writeSeo',
            ActiveRecord::EVENT_BEFORE_DELETE => 'deleteSeo',
        ];
    }

    /**
     * @param $event
     *
     * @throws \Exception
     */
    public function deleteSeo($event)
    {
        $seo = Seo::find()->where(['model_name' => $this->model, 'model_id' => $event->sender->id])->one();
        if ($seo) {
            $seo->delete();
        }
    }


    /**
     * @param $event
     */
    public function writeSeo($event)
    {
        $seo = Seo::findSeo($this->model, $event->sender->id);
        $ownerSeoData = (method_exists($event->sender, 'buildSeoData')) ? $event->sender->buildSeoData() : null;

        if ($seo == null) {
            $seo = new Seo();
        }

        $title = trim(str_replace('  ', ' ', $event->sender->getMlTitle('ru')));

        $seo->load(\Yii::$app->request->post());

        if ($seo->external_link == '' || $seo->isNewRecord) {
            if (method_exists($event->sender, 'buildUrl')) {
                if ($title) {
                    $seo->external_link = trim($event->sender->buildUrl() . '/' . $this::generateSlug($title), '/');
                } else {
                    $seo->external_link = trim($event->sender->buildUrl() . '/' . $event->sender->id, '/');
                }
            } else {
                $seo->external_link = $this->checkUniqueUrl(
                    ($this->view_category ? $this->view_category . "/" : "") . $this::generateSlug($title),
                    $event->sender->id
                );
            }
        }

        if ($ownerSeoData) {
            if ($seo->title_uk == null) {
                $seo->title_uk = $ownerSeoData['title_uk'];
            }
            if ($seo->title_ru == null) {
                $seo->title_ru = $ownerSeoData['title_ru'];
            }
            if ($seo->title_en == null) {
                $seo->title_en = $ownerSeoData['title_en'];
            }

            if ($seo->description_uk == null) {
                $seo->description_uk = $ownerSeoData['description_uk'];
            }
            if ($seo->description_ru == null) {
                $seo->description_ru = $ownerSeoData['description_ru'];
            }
            if ($seo->description_en == null) {
                $seo->description_en = $ownerSeoData['description_en'];
            }

            if ($seo->keywords_uk == null) {
                $seo->keywords_uk = $ownerSeoData['keywords_uk'];
            }
            if ($seo->keywords_ru == null) {
                $seo->keywords_ru = $ownerSeoData['keywords_ru'];
            }
            if ($seo->keywords_en == null) {
                $seo->keywords_en = $ownerSeoData['keywords_en'];
            }
        }

        if ($seo->title_ru == null) {
            $seo->title_ru = $this->owner->title_ru;
        }

        $seo->internal_link = $this->view_action;
        $seo->model_name = $this->model;
        $seo->model_id = $event->sender->id;

        $seo->save(false);
    }

    static function generateSlug($string, $replacement = '_', $lowercase = true, $sourceLang = null)
    {
        $string = mb_strtolower($string);

        if ($sourceLang == 'ru') {
            $string = Inflector::slug($string, $replacement);
        } else {
            $string = str_ireplace(
                ['й', 'с', 'ц', 'у', 'ш', 'щ', 'х', 'є', 'я', 'ч', 'ж', 'ю'],
                ['y', 's', 'ts', 'u', 'sh', 'shch', 'kh', 'ye', 'ya', 'ch', 'zh', 'yu'],
                $string
            );
            $string = Inflector::transliterate($string);
            $string = preg_replace('/[^a-zA-Z0-9\.=\s—–-]+/u', '', $string);
            $string = preg_replace('/[=\s—–-]+/u', $replacement, $string);
            $string = str_replace('.', "_", $string);
            $string = trim($string, $replacement);
        }
        return $lowercase ? mb_strtolower($string) : $string;
    }

    /**
     * @param $url
     * @param $id
     *
     * @return string
     */
    public function checkUniqueUrl($url, $id)
    {
        $result = Seo::find()->where(['external_link' => $url])->andWhere('id!=' . $id)->limit(1)->one();

        if ($result != null) {
            return $url . '_' . $id;
        }

        return $url;
    }

    private function defaultSeoData()
    {
        return [
            'title_uk'       => '',
            'title_ru'       => '',
            'title_en'       => '',
            'description_uk' => '',
            'description_ru' => '',
            'description_en' => '',
            'keywords_uk'    => '',
            'keywords_ru'    => '',
            'keywords_en'    => '',
        ];
    }
}
