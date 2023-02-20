<?php

namespace SysProd\RemoveButton;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use SysProd\modal\Modal;
use kartik\icons\Icon;

/**
 * Created by PhpStorm.
 * User: SysProd
 * Date: 15.08.17
 * Time: 9:31
 *
 * @property string $modelNameId
 * @property string $bodyNameModel
 * @property string $bodyTittle
 * @property string $defaultTranslationCategory
 * @property string $headerMessage
 * @property string $bodyTemplate
 */
class RemoveModal extends Widget
{

    public $modelNameId = 'delete-category-confirmation';
    public $bodyNameModel = 'modalContent-delete_data';
    public $bodyTittle = 'Deleting this category permanently deletes all subcategories and attachments.';

    /** @var string Default labels translation category */
    private $defaultTranslationCategory = 'app.remove';

    /** @var string Default message for the header*/
    public $headerMessage = 'Warning!';

    public $bodyTemplate = <<< HTML
        <div id="{bodyName}" class="{bodyClass}">
            {body}
        </div>
HTML;

    /**
     * @inheritdoc
     */
    public function init()
    {
        self::registerTranslations();
        parent::init();
    }

    private function registerTranslations()
    {
        Yii::$app->i18n->translations[$this->defaultTranslationCategory] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@vendor/SysProd/yii2-removebutton-widget/messages',
        ];
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->runJsScript();
        $this->runModal();
    }

    /**
     * Run JavaScript
     */
    private function runJsScript()
    {
        $view = $this->getView();

        $js = <<<JS
            $('#{$this->modelNameId} [data-action="confirm"]').click(function() {
        var modal = $(this).parents('.modal');
        var data =  typeof(modal.attr('data-items')) == "string" && modal.attr('data-items').length > 0
            ? {'items': modal.attr('data-items').split(',')}
            : {} ;
        $.post( modal.attr('data-url'), data, function(val){ return true; });
        return true;
    });
JS;
        $view->registerJs($js);

    }

    /**
     * Install modal
     */
    private function runModal()
    {
        $content = strtr($this->bodyTemplate, [
            '{bodyName}' => Html::encode($this->bodyNameModel),
            '{bodyClass}' => 'margin no-print',
            '{body}' => Yii::t($this->defaultTranslationCategory, Html::encode($this->bodyTittle)),
        ]);

        Modal::begin(
            [
                'typeModal' => Modal::TYPE_DANGER,
                'id' => $this->modelNameId,
                'footerButton' => [
                    'encode' => false,
                    'labelDelete' => [
                        'label' => Yii::t('app', 'Delete'),
                        'class' => Modal::STYLE_DANGER,
                    ],
                    'labelCancel' => [
                        'label' => Yii::t('app', 'Cancel'),
                        'class' => Modal::STYLE_DEFAULT,
                    ],
                ],
                'header' => Yii::t($this->defaultTranslationCategory, Html::encode($this->headerMessage)),
                'headerIcon' => Icon::show('exclamation-triangle', ['class' => 'fa-lg']),
                'headerOptions' => ['class' => 'box-header with-border'],
                'bodyOptions' => ['class' => 'box-body'],
            ]
        );

        echo $content;

        Modal::end();

    }

}