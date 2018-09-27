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
//        if (empty($this->attributes)) {
//            $this->attributes = [
//                BaseActiveRecord::EVENT_BEFORE_INSERT => [$this->createdByAttribute, $this->updatedByAttribute],
//                BaseActiveRecord::EVENT_BEFORE_UPDATE => $this->updatedByAttribute,
//            ];
//        }
        //if (!empty($this->attributes)) {
        $this->attributes = [
            BaseActiveRecord::EVENT_BEFORE_INSERT => $this->dateAttribute,
            BaseActiveRecord::EVENT_BEFORE_UPDATE => $this->dateAttribute,
        ];
        //}
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
            $attribute = $this->dateAttribute;
            $date = $this->owner->{$attribute};

            if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date)) {
                return $date;
            } else if (preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1]/(0[1-9]|1[0-2])/[0-9]{4})$/", $date)) {
                $exp = explode('/', $date);
                if (count($exp) !== 3) {
                   // throw new \Exception(strtr('Disallow format date "{date}"', ['{date}' => $date]));
                }
                $year = intval($exp[2]);

                return strval($year - $this->yearDistance) . '-' . $exp[1] . '-' . $exp[0];
            } else {
//                throw new \Exception(strtr('Disallow format date "{date}"', ['{date}' => $date]));
//                return null;
            }
        } elseif (in_array($event->name, [BaseActiveRecord::EVENT_AFTER_FIND, BaseActiveRecord::EVENT_AFTER_INSERT, BaseActiveRecord::EVENT_AFTER_UPDATE])) {
            return $this->getUnConvertValue();
        }
    }

    /**
     * @param array $value
     * @param $type
     * @return string
     */
    private function getConvertValue($event) {
        //if ($this->value === null) {
        echo $event->name;
        $attribute = $this->dateAttribute;
        $value = $this->owner->{$attribute};
        echo $this->dateAttribute;
        echo $value;
        echo $this->checkFormatDate($value);
        exit();
        //$value;
        if (!$value) {
            return null;
        }
//            $exp = explode('/', $value);
//            if (count($exp) !== 3) {
//                return null;
//            }
//            $year = intval($exp[2]);
//             echo strval($year - $this->yearDistance).'-'.$exp[1].'-'.$exp[0];
//             exit();


        if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date)) {
            return $date;
        } else if (preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1]/(0[1-9]|1[0-2])/[0-9]{4})$/", $date)) {
            $exp = explode('/', $date);
            if (count($exp) !== 3) {
                throw new \Exception(strtr('Disallow format date "{date}"', ['{date}' => $date]));
            }
            $year = intval($exp[2]);

            return strval($year - $this->yearDistance) . '-' . $exp[1] . '-' . $exp[0];
        } else {
            throw new \Exception(strtr('Disallow format date "{date}"', ['{date}' => $date]));
            return null;
        }

        return $this->checkFormatDate($value);
//        }
//        return parent::getValue($event);
    }

    /**
     * @param $value
     * @param $type
     * @return array
     */
    private function getUnConvertValue($value, $type) {

//        if ($value && $type == self::DEFAULT_CONVERT_TYPE) {
//            try {
//                $value = unserialize($value);
//            } catch (\Exception $e) {
//                trigger_error($e);
//            }
//        } else {
//            $value = Json::decode($value);
//        }

        return $this->value;
    }

    private function checkFormatDate($date) {
        #2012-09-12
        if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date)) {
            return $date;
        } else if (preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1]/(0[1-9]|1[0-2])/[0-9]{4})$/", $date)) {
            $exp = explode('/', $date);
            if (count($exp) !== 3) {
                throw new \Exception(strtr('Disallow format date "{date}"', ['{date}' => $date]));
            }
            $year = intval($exp[2]);

            return strval($year - $this->yearDistance) . '-' . $exp[1] . '-' . $exp[0];
        } else {
            throw new \Exception(strtr('Disallow format date "{date}"', ['{date}' => $date]));
            return null;
        }
    }

}

?>