<?php

namespace SysProd\RemoveButton;

use yii\base\InvalidParamException;
use yii\base\Widget;
use yii\helpers\Html;
use kartik\icons\Icon;

/**
 * Кнопка удаления выбраных элементов в GridView
 * Class RemoveAllButton
 * @package app\widgets
 *
 * @property string $url
 * @property string $gridSelector
 * @property array $htmlOptions
 * @property string $modalSelector
 */
class RemoveAllButton extends Widget
{
    public $url;
    public $gridSelector;
    public $htmlOptions = [];
    public $modalSelector = 'delete-confirmation';

    public function init()
    {
        if (!isset($this->url, $this->gridSelector)) {
            throw new InvalidParamException('Attribute \'url\' or \'gridSelector\' is not set');
        }

        if (!isset($this->htmlOptions['id'])) {
            $this->htmlOptions['id'] = 'deleteItems';
        }
        Html::addCssClass($this->htmlOptions, 'btn');
    }

    public function run()
    {
        $this->registerScript();
        return $this->renderButton();
    }

    protected function renderButton()
    {
        return Html::button(
            Icon::show('trash'),
            $this->htmlOptions
        );
    }

    protected function registerScript()
    {
        $this->view->registerJs("
            jQuery('#{$this->htmlOptions['id']}').on('click', function() {
                var items =  $('{$this->gridSelector}').yiiGridView('getSelectedRows');
                if (items.length) {
                    jQuery('#{$this->modalSelector}').attr('data-url', '{$this->url}').attr('data-items', items).modal('show');
                }
                return false;
            });
        ");
    }
}
 