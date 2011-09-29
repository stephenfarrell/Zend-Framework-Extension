<?php
/**
 * Class for storing a logged-in users details for their session
 *
 * @author Stephen Farrell <stephen@stephenfarrell.net>
 * @copyright (C) 2010 Stephen Farrell - All Rights Reserved
 * @license Closed - Not for redistribution
 * @version 1.0
 *
 */
class SF_Auth_Identity {
	private $username = '';
	private $id = '';
	private $fullname = '';
	
	public function __construct($id, $username, $fullname) {
		$this->setUserId($id);
		$this->setUsername($username);
		$this->setFullName($fullname);
	}
	
	public function setUserId($id) {
		$this->id = $id;
		
		return $this;
	}
	
	public function setUsername($username) {
		$this->username = $username;
		
		return $this;
	}
	
	public function setFullName($fullName) {
		$this->fullname = $fullName;
		
		return $this;
	}
	
	public function getUserId() {
		return $this->id;
	}
	
	public function getUsername() {
		return $this->username;
	}
	
	public function getFullName() {
		return $this->fullname;
	}
}