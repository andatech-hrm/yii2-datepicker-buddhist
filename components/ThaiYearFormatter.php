<?php

/**
 * ThaiYearFormatter
 *
 * Convert Year to Thai Buddhist Era Year
 *
 * @package    dixonsatit
 * @subpackage thaiYearFormatter
 * @author     Satit Seethaphon <dixonsatit@gmail.com>
 */

namespace andahrm\datepicker\components;

use yii\i18n\Formatter;
use IntlDateFormatter;
use DateTime;
use DateTimeZone;
use yii\helpers\FormatConverter;
use Yii;
use yii\base\InvalidParamException;

class ThaiYearFormatter extends Formatter {

    private $_intlLoaded = false;
    private $_dateFormats = [
        'short' => 3, // IntlDateFormatter::SHORT,
        'medium' => 2, // IntlDateFormatter::MEDIUM,
        'long' => 1, // IntlDateFormatter::LONG,
        'full' => 0, // IntlDateFormatter::FULL,
    ];

    public function init() {
        if ($this->timeZone === null) {
            $this->timeZone = Yii::$app->timeZone;
        }
        if ($this->locale === null) {
            $this->locale = Yii::$app->language;
        }
        if ($this->booleanFormat === null) {
            $this->booleanFormat = [Yii::t('yii', 'No', [], $this->locale), Yii::t('yii', 'Yes', [], $this->locale)];
        }
        if ($this->nullDisplay === null) {
            $this->nullDisplay = '<span class="not-set">' . Yii::t('yii', '(not set)', [], $this->locale) . '</span>';
        }
        $this->_intlLoaded = extension_loaded('intl');
        if (!$this->_intlLoaded) {
            if ($this->decimalSeparator === null) {
                $this->decimalSeparator = '.';
            }
            if ($this->thousandSeparator === null) {
                $this->thousandSeparator = ',';
            }
        }
    }

    public function asDate($value, $format = null) {
        if ($format === null) {
            $format = $this->dateFormat;
        }
        $gdate = $this->formatDateTimeValue($value, $format, 'date');
        return $value ? $this->toThaiYear($gdate, $value) : Yii::$app->formatter->nullDisplay;
    }

    public function asDatetime($value, $format = null) {
        if ($format === null) {
            $format = $this->datetimeFormat;
        }
        $gdate = $this->formatDateTimeValue($value, $format, 'datetime');
        return $value ? $this->toThaiYear($gdate, $value) : null;
    }

    protected function toThaiYear($gdate, $value) {
        if ($this->checkThaiLocale()) {
            $year = parent::asDate($value, "php:Y");
            return str_replace($year, intval($year) + 543, $gdate);
        } else {
            return $gdate;
        }
    }

    private function formatDateTimeValue($value, $format, $type) {
        $timeZone = $this->timeZone;
        // avoid time zone conversion for date-only values
        if ($type === 'date') {
            list($timestamp, $hasTimeInfo) = $this->normalizeDatetimeValue($value, true);

            if (!$hasTimeInfo) {
                $timeZone = $this->defaultTimeZone;
            }
        } else {
            $timestamp = $this->normalizeDatetimeValue($value);
        }
        if ($timestamp === null) {
            return $this->nullDisplay;
        }

        // intl does not work with dates >=2038 or <=1901 on 32bit machines, fall back to PHP
        $year = $timestamp->format('Y');
        if ($this->_intlLoaded && !(PHP_INT_SIZE === 4 && ($year <= 1901 || $year >= 2038))) {
            if (strncmp($format, 'php:', 4) === 0) {
                $format = FormatConverter::convertDatePhpToIcu(substr($format, 4));
            }
            if (isset($this->_dateFormats[$format])) {
                if ($type === 'date') {
                    $formatter = new IntlDateFormatter($this->locale, $this->_dateFormats[$format], IntlDateFormatter::NONE, $timeZone, $this->calendar);
                } elseif ($type === 'time') {
                    $formatter = new IntlDateFormatter($this->locale, IntlDateFormatter::NONE, $this->_dateFormats[$format], $timeZone, $this->calendar);
                } else {
                    $formatter = new IntlDateFormatter($this->locale, $this->_dateFormats[$format], $this->_dateFormats[$format], $timeZone, $this->calendar);
                }
            } else {
                $formatter = new IntlDateFormatter($this->locale, IntlDateFormatter::NONE, IntlDateFormatter::NONE, $timeZone, $this->calendar, $format);
            }
            if ($formatter === null) {
                throw new InvalidConfigException(intl_get_error_message());
            }
            // make IntlDateFormatter work with DateTimeImmutable
            if ($timestamp instanceof \DateTimeImmutable) {
                $timestamp = new DateTime($timestamp->format(DateTime::ISO8601), $timestamp->getTimezone());
            }
            return $formatter->format($timestamp);
        } else {
            if (strncmp($format, 'php:', 4) === 0) {
                $format = substr($format, 4);
            } else {
                $format = FormatConverter::convertDateIcuToPhp($format, $type, $this->locale);
            }
            if ($timeZone != null) {
                if ($timestamp instanceof \DateTimeImmutable) {
                    $timestamp = $timestamp->setTimezone(new DateTimeZone($timeZone));
                } else {
                    $timestamp->setTimezone(new DateTimeZone($timeZone));
                }
            }
            return $timestamp->format($format);
        }
    }

    protected function normalizeDatetimeValue($value, $checkTimeInfo = false) {
        // checking for DateTime and DateTimeInterface is not redundant, DateTimeInterface is only in PHP>5.5
        if ($value === null || $value instanceof DateTime || $value instanceof DateTimeInterface) {
            // skip any processing
            return $checkTimeInfo ? [$value, true] : $value;
        }
        if (empty($value)) {
            $value = 0;
        }
        try {
            if (is_numeric($value)) { // process as unix timestamp, which is always in UTC
                $timestamp = new DateTime();
                $timestamp->setTimezone(new DateTimeZone('UTC'));
                $timestamp->setTimestamp($value);
                return $checkTimeInfo ? [$timestamp, true] : $timestamp;
            } elseif (($timestamp = DateTime::createFromFormat('Y-m-d', $value, new DateTimeZone($this->defaultTimeZone))) !== false) { // try Y-m-d format (support invalid dates like 2012-13-01)
                return $checkTimeInfo ? [$timestamp, false] : $timestamp;
            } elseif (($timestamp = DateTime::createFromFormat('Y-m-d H:i:s', $value, new DateTimeZone($this->defaultTimeZone))) !== false) { // try Y-m-d H:i:s format (support invalid dates like 2012-13-01 12:63:12)
                return $checkTimeInfo ? [$timestamp, true] : $timestamp;
            }
            // finally try to create a DateTime object with the value
            if ($checkTimeInfo) {
                $timestamp = new DateTime($value, new DateTimeZone($this->defaultTimeZone));
                $info = date_parse($value);
                return [$timestamp, !($info['hour'] === false && $info['minute'] === false && $info['second'] === false)];
            } else {
                return new DateTime($value, new DateTimeZone($this->defaultTimeZone));
            }
        } catch (\Exception $e) {
            throw new InvalidParamException("'$value' is not a valid date time value: " . $e->getMessage()
            . "\n" . print_r(DateTime::getLastErrors(), true), $e->getCode(), $e);
        }
    }

    /**
     * replace Year
     * @param  string $strDate
     * @return string date
     */
    public function replaceYear($strDate) {
        return str_replace('ค.ศ.', 'พ.ศ.', $strDate);
    }

    /**
     * check is Thai Locale
     * @return string date
     */
    public function checkThaiLocale() {
        return (strtolower($this->locale) === 'th' || $this->locale == 'th_TH' || $this->locale == 'th-TH');
    }

    public static function toDb($value) {
        // echo $this->owner->scenario;

        if (!$value) {
            return null;
        }
        $exp = explode('/', $value);
        if (count($exp) !== 3) {
            return null;
        }
        $year = intval($exp[2]);

        // echo strval($year - $this->yearDistance).'-'.$exp[1].'-'.$exp[0];
        // exit();
        return strval($year - 543) . '-' . $exp[1] . '-' . $exp[0];
    }

}
