<?php
require_once 'Zend/Validate/Abstract.php';
//require_once 'Zend/Validate/Hostname.php';

require_once 'Zend/Validate/Exception.php';
 
class SF_Validate_FieldCompare extends Zend_Validate_Abstract
{
    const MSG_NOT_EQ = 'fieldNotSame';
    const MSG_NOT_GT = 'fieldNotGreaterThan';
    const MSG_NOT_GE = 'fieldNotGreaterThanEqual';
    const MSG_NOT_LT = 'fieldNotLessThan';
    const MSG_NOT_LE = 'fieldNotLessThanEqual';

 
    const EQ = 'eq';
    const GT = 'gt';
    const GE = 'ge';
    const LT = 'lt';
    const LE = 'le';
 
    protected $_messageVariables = array(

        'key' => '_key',
        'key_value' => '_key_value'
    );
 
    protected $_messageTemplates = array(

        self::MSG_NOT_EQ => "'%value%' is not equal to '%key_value%'",
        self::MSG_NOT_GT => "'%value%' is not greater than '%key_value%'",
        self::MSG_NOT_GE => "'%value%' is not greater than or equal to '%key_value%'",
        self::MSG_NOT_LT => "'%value%' is not less than '%key_value%'",
        self::MSG_NOT_LE => "'%value%' is not less than or equal to '%key_value%'",
    );

 
    protected static $_tmp_operators = null;
    protected static $_tmp_values    = null;
    protected static $_key_errors    = null;
    protected static $_key_messages  = null;

 
 
    protected $_key      = null;
    protected $_operator = null;
    protected $_key_value = null;

 
    public function __construct($key, $operator = null)
    {
        $this->_key = $key;
        $this->_operator = $operator;
        self::$_key_errors[$key] = array();
        self::$_key_messages[$key] = array();
    }

 
    public function isValid($value)
    {
        if ($this->_operator && isset(self::$_tmp_values[$this->_key]))

        {
            $this->_key_value = self::$_tmp_values[$this->_key];
        }
        else if (!$this->_operator && isset(self::$_tmp_operators[$this->_key]))

        {
            $this->_key_value = $value;
            $this->_operator  = self::$_tmp_operators[$this->_key];
            $value            = self::$_tmp_values[$this->_key];
        }

        else if ($this->_operator && !isset(self::$_tmp_values[$this->_key]))

        {
            self::$_tmp_operators[$this->_key] = $this->_operator;
            self::$_tmp_values[$this->_key] = $value;
        }

        else if (!$this->_operator && isset(self::$_tmp_values[$this->_key]))

        {
            throw new Zend_Validate_Exception(
                'Field Compare Validator : operator does not specified(key=' .
                $this->_key . ')');
        }

        else if (!$this->_operator && !isset(self::$_tmp_operators[$this->_key]))

        {
            self::$_tmp_values[$this->_key] = $value;
        }
 
        $this->_setValue($value);
        if ($this->_key_value && $this->_operator)

        {
            switch($this->_operator)
            {
            case self::EQ :
                if ($value == $this->_key_value)

                {
                    return true;
                }
                $this->_error(self::MSG_NOT_EQ);
                return false;
            case self::GT :
                if ($value > $this->_key_value)

                {
                    return true;
                }
                $this->_error(self::MSG_NOT_GT);
                return false;
            case self::GE :
                if ($value >= $this->_key_value)

                {
                    return true;
                }
                $this->_error(self::MSG_NOT_GE);
                return false;
            case self::LT :
                if ($value < $this->_key_value)

                {
                    return true;
                }
                $this->_error(self::MSG_NOT_LT);
                return false;
            case self::LE :
                if ($value <= $this->_key_value)

                {
                    return true;
                }
                $this->_error(self::MSG_NOT_LE);
                return false;
            }

           throw new Zend_Validate_Exception(
               'Field Compare Validator : operator not found(key=' . $this->_key .
               ',op=' . $this->_operator . ')');
            return false;
        }

        return true;
    }
 
    /**
     * Sets the value to be validated and clears the messages and errors arrays
     *
     * @param  mixed $value
     * @return void
     */
    protected function _setValue($value)

    {
        parent::_setValue($value);
        self::$_key_errors[$this->_key] = array();
        self::$_key_messages[$this->_key] = array();
    }

 
    /**
     * @param  string $messageKey OPTIONAL
     * @param  string $value      OPTIONAL
     * @return void
     */
    protected function _error($messageKey = null, $value = null)

    {
        if ($messageKey === null) {
            $keys = array_keys($this->_messageTemplates);
            $messageKey = current($keys);
        }

        if ($value === null) {
            $value = $this->_value;
        }

        self::$_key_errors[$this->_key][] = $messageKey;
        self::$_key_messages[$this->_key][$messageKey] =
            $this->_createMessage($messageKey, $value);
    }

 
    /**
     * Returns array of validation failure messages
     *
     * @return array
     */
    public function getMessages()
    {
        if ($this->_operator)

        {
            return self::$_key_messages[$this->_key];
        }
        return array();
    }

 
    /**
     * Returns array of validation failure message codes
     *
     * @return array
     * @deprecated Since 1.5.0
     */
    public function getErrors()
    {
        if ($this->_operator)

        {
            return self::$_key_errors[$this->_key];
        }
        return array();
    }

 
}