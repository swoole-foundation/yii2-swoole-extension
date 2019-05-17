<?php
/**
 * @author xialeistudio
 * @date 2019-05-17
 */

namespace tests\controllers;

use tests\tasks\DemoTask;
use Yii;
use yii\db\Exception;
use yii\web\Controller;

class SiteController extends Controller
{
    public function actionIndex()
    {
        return [
            'name' => Yii::$app->name,
            'version' => Yii::$app->version
        ];
    }

    public function actionDump()
    {
        return [
            '$_GET' => Yii::$app->request->get(),
            '$_POST' => Yii::$app->request->post()
        ];
    }

    public function actionCache()
    {
        return [
            'data' => Yii::$app->cache->getOrSet('test', function () {
                return time();
            }, 10)
        ];
    }

    /**
     * @return array|false
     * @throws Exception
     */
    public function actionDb()
    {
        return Yii::$app->db->createCommand('SELECT VERSION() as version')->queryOne();
    }

    public function actionTask()
    {
        Yii::$app->server->task([[DemoTask::class, 'demo'], ['a', '1']]);
        return [];
    }
}