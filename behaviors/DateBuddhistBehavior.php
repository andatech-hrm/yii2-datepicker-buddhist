<?php
namespace andahrm\datepicker\behaviors;

use yii\db\BaseActiveRecord;
use yii\behaviors\AttributeBehavior;

class DateBuddhistBehavior extends AttributeBehavior
{
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
    public function init()
    {
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
    protected function getValue($event)
    {
        // echo $this->owner->scenario;
        if ($this->value === null) {
            $attribute = $this->dateAttribute;
            $value = $this->owner->{$attribute};
            if(!$value){
                return null;
            }
            $exp = explode('/', $value);
            if(count($exp) !== 3){
                return null;
            }
            $year = intval($exp[2]);
            
            // echo strval($year - $this->yearDistance).'-'.$exp[1].'-'.$exp[0];
            // exit();
            return strval($year - $this->yearDistance).'-'.$exp[1].'-'.$exp[0];
        }
        return parent::getValue($event);
    }
}
?>