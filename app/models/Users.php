<?php
namespace Realyagu\Models;

use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Email as EmailValidator;
use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\Numericality;

/**
 * Realyagu\Models\Users
 * All the users registered in the application
 */
class Users extends Model
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
    public $shopsId;
    
    /**
     *
     * @var string
     */
    public $userName;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $email;
    
    /**
     *
     * @var integer
     */
    public $grade;

    /**
     *
     * @var string
     */
    public $password;
   
    /**
     *
     * @var integer
     */
    public $mustChangePassword;
    
    /**
     *
     * @var string
     */
    public $phoneNumber;

    /**
     *
     * @var integer
     */
    public $status;

    /**
     *
     * @var dateTime
     */
    public $createdAt;

    /**
     * Before create the user assign a password
     */
    public function beforeValidationOnCreate()
    {
    	$this->createdAt = new \Phalcon\Db\RawValue('now()');
    	
        if (empty($this->password)) {

            // Generate a plain temporary password
            $tempPassword = preg_replace('/[^a-zA-Z0-9]/', '', base64_encode(openssl_random_pseudo_bytes(12)));

            // The user must change its password in first login
            $this->mustChangePassword = 'Y';

            // Use this password as default
            $this->password = $this->getDI()
                ->getSecurity()
                ->hash($tempPassword);
        } else {
            // The user must not change its password in first login
            $this->mustChangePassword = 'N';
        }

        //이메일 인증을 할경우 인증후
        if ($this->getDI()->get('config')->useMail) {
        	//최초 회원 상태 비활성화
        	$this->status = $this->getDI()->get('config')->userStatus->notAuth;
        } else {
        	//최초 회원 상태 활성화
        	$this->status = $this->getDI()->get('config')->userStatus->live;
        }
    }

    /**
     * Send a confirmation e-mail to the user if the account is not active
     */
    public function afterSave()
    {
        
    }

    /**
     * Validate that emails are unique across users
     */
    public function validation()
    {
        $validator = new Validation();

        $validator->add('email', new EmailValidator([
        	'message' => '이메일 형식이 잘못 되었습니다.'
        ]));
        
        $validator->add('email', new Uniqueness([
            'message' => '이미 등록된 이메일 입니다.'
        ]));
        
        $validator->add('userName', new Uniqueness([
        	'message' => '이미 등록된 아이디 입니다.'
        ]));
        
        return $this->validate($validator);
    }

    public function initialize()
    {
        $this->hasMany('id', __NAMESPACE__ . '\SuccessLogins', 'usersId', [
            'alias' => 'successLogins',
            'foreignKey' => [
                'message' => 'User cannot be deleted because he/she has activity in the system'
            ]
        ]);

        $this->hasMany('id', __NAMESPACE__ . '\PasswordChanges', 'usersId', [
            'alias' => 'passwordChanges',
            'foreignKey' => [
                'message' => 'User cannot be deleted because he/she has activity in the system'
            ]
        ]);

        $this->hasMany('id', __NAMESPACE__ . '\ResetPasswords', 'usersId', [
            'alias' => 'resetPasswords',
            'foreignKey' => [
                'message' => 'User cannot be deleted because he/she has activity in the system'
            ]
        ]);
    }
}
