<?php
namespace Realyagu\Models;
use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness;

/**
 * Realyagu\Models\Shops
 * All the users registered in the application
 */
class Shops extends Model
{
	/**
	 *
	 * @var integer
	 */
	public $id;
	
	/**
	 *
	 * @var integer
	 */
	public $usersId;
	
	/**
	 *
	 * @var string
	 */
	public $name;
	
	/**
	 *
	 * @var string
	 */
	public $site;
	
	/**
	 *
	 * @var string
	 */
	public $businessNumber;
	
	/**
	 *
	 * @var string
	 */
	public $businessConditions;
	
	/**
	 *
	 * @var string
	 */
	public $businessType;
	
	/**
	 *
	 * @var string
	 */
	public $phoneNumber;
	
	/**
	 *
	 * @var string
	 */
	public $zipCode;
	
	/**
	 *
	 * @var string
	 */
	public $address1;
	
	/**
	 *
	 * @var string
	 */
	public $address2;
	
	/**
	 *
	 * @var string
	 */
	public $status;
	
	/**
	 *
	 * @var dateTime
	 */
	public $createAt;	
}