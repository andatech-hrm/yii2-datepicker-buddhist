# yii2-datepicker-buddhist

## Install
composer require kuakling/yii2-datepicker-buddhist "dev-master"

## Usage
~~~ php
use yii\bootstrap\ActiveForm;
use andahrm\datepicker\DatePicker;

$form = ActiveForm::begin();

echo $form->field($model, 'birthday')->widget(DatePicker::className());

ActiveForm::end();

echo DatePicker::widget([
    'name' => 'mydate',
]);
