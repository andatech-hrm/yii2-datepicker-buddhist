<?php

namespace andahrm\datepicker\behaviors;

use yii\db\BaseActiveRecord;
use yii\behaviors\AttributeBehavior;

class DateBuddhistBehavior extends AttributeBehavior {

    public $dateAttribute = 'date';
    public $yearDistance = 543;

    /**
     * @inheritdoc
     *
     * In case, when the value is `null`, the result of the PHP function [time()](http://php.net/manual/en/function.time.php)
     * will be used as value.
     */
    public $value;

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();

        $this->attributes = [
            BaseActiveRecord::EVENT_BEFORE_INSERT => $this->dateAttribute,
            BaseActiveRecord::EVENT_BEFORE_UPDATE => $this->dateAttribute,
        ];
    }

    /**
     * @inheritdoc
     *
     * In case, when the [[value]] is `null`, the result of the PHP function [time()](http://php.net/manual/en/function.time.php)
     * will be used as value.
     */
    protected function getValue($event) {
        // echo $this->owner->scenario;

        if (in_array($event->name, [BaseActiveRecord::EVENT_BEFORE_INSERT, BaseActiveRecord::EVENT_BEFORE_UPDATE])) {
            return $this->getConvertValue();
        } elseif (in_array($event->name, [BaseActiveRecord::EVENT_AFTER_FIND, BaseActiveRecord::EVENT_AFTER_INSERT, BaseActiveRecord::EVENT_AFTER_UPDATE])) {
            return $this->getUnConvertValue();
        }
    }

    private function getConvertValue() {
        $attribute = $this->dateAttribute;
        $value = $this->owner->{$attribute};
        return $this->checkFormatDate($value);
    }

    private function getUnConvertValue() {
        return $this->value;
    }

    private function checkFormatDate($date) {
//        echo $date;
//        exit();
        #2012-09-12
        if (\DateTime::createFromFormat("Y-m-d", $date)) {
//            echo "1:";
//            echo $date;
//            exit();
            return $date;
        } else if (\DateTime::createFromFormat("d/m/Y", $date)) {
//            echo "2:";
//            echo $date;
//            exit();
            $exp = explode('/', $date);
            if (count($exp) !== 3) {
                
            }
            $year = intval($exp[2]);
            return strval($year - $this->yearDistance) . '-' . $exp[1] . '-' . $exp[0];
        }
    }

}

?>