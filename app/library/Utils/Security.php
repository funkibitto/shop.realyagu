<?php
namespace Realyagu\Utils;

class Security extends \Phalcon\Security
{
	public function checkToken($tokenKey = null,$tokenValue = null, $destroyIfValid = false)
	{
		return parent::checkToken($tokenKey,$tokenValue, $destroyIfValid);
	}
}