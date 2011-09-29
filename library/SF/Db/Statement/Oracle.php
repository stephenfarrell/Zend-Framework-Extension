<?php
/**
 * Class for preparing a statement to be executed against an Oracle
 * database.
 *
 * This class uses Zend_Db_Statement_Oracle's basic functionality
 * however it extends the class to expose bindParam as a public method.
 * This allows the bind type to be specified which enables the use of
 * refcursors as a return type from a stored procedure
 *
 * @author Stephen Farrell <stephen@stephenfarrell.net>
 * @copyright (C) 2010 Stephen Farrell - All Rights Reserved
 * @license Closed - Not for redistribution
 * @version 1.0
 *
 * @uses Zend_Db_Statement_Oracle
 */
class SF_Db_Statement_Oracle extends Zend_Db_Statement_Oracle {

	public function __construct($adapter, $sql, $stmt = null)
    {
        $this->_adapter = $adapter;

		if(null !== $stmt) {
			$this->_stmt = $stmt;
		} else {
			if ($sql instanceof Zend_Db_Select) {
				$sql = $sql->assemble();
			}
			$this->_parseParameters($sql);
			$this->_prepare($sql);

			$this->_queryId = $this->_adapter->getProfiler()->queryStart($sql);
		}
    }

	public function bindParam($parameter, &$variable, $type = null, $length = null, $options = null) {
		return $this->_bindParam($parameter, $variable, $type, $length, $options);
	}

}