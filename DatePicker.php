<?php
namespace andahrm\datepicker;

use Yii;
use yii\bootstrap\Html;
/**
 * \andahrm\datepicker\DatePicker.
 *
 * @author kuakling <kuakling@gmail.com>
 * @since 1.0
 */
class DatePicker extends \yii\widgets\InputWidget
{
    public $options = [];
    /**
     * Renders the widget.
     */
    public function run()
    {
        $this->renderInput();
        $asset = DatePickerAsset::register($this->getView());
        $this->registerAsset($asset);
    }
    
    protected function renderInput()
    {
        $options = array_replace_recursive($this->options, ['class' => 'form-control']);
        $html = '<div class="input-group">';
        if($this->hasModel()){
            $attribute =  preg_replace('/[\[{\(].*[\]}\)]/U' , '', $this->attribute);
            $this->value = $this->model->{$attribute};
            $date = \DateTime::createFromFormat('Y-m-d', $this->value);
            $this->value = $date ? Yii::$app->formatter->asDate($this->value, 'php:d/m/Y') : null;
            $options = array_replace_recursive($options, ['value' => $this->value]);
            
            $html .= Html::activeTextInput($this->model, $this->attribute, $options) ;
            // $this->name = Html::getInputName($this->model, $this->attribute);
            //$html .= Html::textInput($this->name, $this->value, $options);
        }else{
        $html .= Html::textInput($this->name, $this->value, $options);
        }
        
        $html .= '<span class="input-group-addon" style="cursor: pointer;" onclick="$(this).closest(\'.input-group\').find(\'input[type=text]\').focus()">';
        $html .= '<i class="glyphicon glyphicon-calendar"></i>';
        $html .= '</span>';
        $html .= '</div>';
        echo $html;
    }
    
    public function registerAsset($asset)
    {
        $view = $this->getView();
        $inputId = $this->options['id'];
        $options = array_merge([
            'language' => 'th-th',
            'autoclose' => true
        ], $this->options);
        $jsonOptions = \yii\helpers\Json::encode($options);
        $js[] = <<< JS
$("#{$inputId}").datepicker($jsonOptions);
JS;
        $view->registerJs(implode("\n", $js));
    }
}
