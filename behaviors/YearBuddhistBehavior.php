<?php
namespace andahrm\datepicker\behaviors;

use yii\db\BaseActiveRecord;
use yii\behaviors\AttributeBehavior;

class YearBuddhistBehavior extends AttributeBehavior
{
    public $attribute = 'year';
    
    
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
            BaseActiveRecord::EVENT_BEFORE_INSERT => $this->attribute,
            BaseActiveRecord::EVENT_BEFORE_UPDATE => $this->attribute,
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
        if ($this->value === null) {
            // $attribute1 = $this->christianAttribute;
            
            // $r = null;
            // switch($event->name) {
            //     case 'afterFind' : $r = (intval($this->owner->{$attribute1}) + $this->yearDistance);
            //         break;
            //     case 'beforeUpdate' : $r = (intval($this->owner->{$attribute2}) - $this->yearDistance);
            //         break;
            //     case 'beforeInsert' : $r = (intval($this->owner->{$attribute2}) - $this->yearDistance);
            //         break;
            // }
            
            // return $r;
            
            $attribute = $this->attribute;
            return (intval($this->owner->{$attribute}) - $this->yearDistance);
        }
        return parent::getValue($event);
    }
}
?>