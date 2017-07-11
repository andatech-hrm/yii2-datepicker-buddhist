<?php
namespace andahrm\datepicker;

use Yii;
use yii\bootstrap\Html;
/**
 * \andahrm\datepicker\YearBuddhist.
 *
 * @author kuakling <kuakling@gmail.com>
 * @since 1.0
 */
class YearBuddhist extends \yii\widgets\InputWidget
{
    public $options = [];
    /**
     * Renders the widget.
     */
    public function run()
    {
        $this->renderInput();
        // $asset = DatePickerAsset::register($this->getView());
        // $this->registerAsset($asset);
    }
    
    protected function renderInput()
    {
        $options = array_replace_recursive($this->options, ['class' => 'form-control']);
        $html = '';
        if($this->hasModel()){
            $attribute =  preg_replace('/[\[{\(].*[\]}\)]/U' , '', $this->attribute);
            $value = $this->model->{$attribute};
            if($value){
                $this->value = intval($value) + 543;
            }
            $options = array_replace_recursive($options, ['value' => $this->value]);
            
            $html .= Html::activeTextInput($this->model, $this->attribute, $options);
        }else{
            $html .= Html::textInput($this->name, $this->value, $options);
        }
        
        echo $html;
    }
    
//     public function registerAsset($asset)
//     {
//         $view = $this->getView();
//         $inputId = $this->options['id'];
//         $options = array_merge([
//             'language' => 'th-th',
//             'autoclose' => true
//         ], $this->options);
//         $jsonOptions = \yii\helpers\Json::encode($options);
//         $js[] = <<< JS
// $("#{$inputId}").datepicker($jsonOptions);
// JS;
//         $view->registerJs(implode("\n", $js));
//     }
}
