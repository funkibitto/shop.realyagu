<?php
namespace Realyagu\Forms;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Submit;
use Phalcon\Forms\Element\Check;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\Identical;
use Phalcon\Validation\Validator\Confirmation;
use Phalcon\Validation\Validator\Regex;

class SignUpForm extends Form
{

    public function initialize($entity = null, $options = null)
    {
    	$userName = new Text('userName', [
            "maxlength"   => 20,
            "placeholder" => "아이디",
        ]);
    	$userName->setLabel('아이디');
    	$userName->setFilters(array('striptags', 'string'));
    	$userName->addValidators([
    			new PresenceOf([
    					'message' => '아이디를 넣어주세요.',
    					'cancelOnFail' => true
    			]),
    			new Regex([
    					'message'    => '아이디가 올바르지 않습니다.',
    					'pattern'    => '/^[a-z]+[a-z0-9]{4,19}$/',
    			])
    	]);
    	$this->add($userName);
    	 
        $name = new Text('name', [
            "maxlength"   => 20,
            "placeholder" => "이름",
        ]);
        $name->setLabel('이름');
        $name->setFilters(array('striptags', 'string'));
        $name->addValidators([
            new PresenceOf([
                'message' => '이름을 넣어주세요.',
            	'cancelOnFail' => true
            ])
        ]);
        $this->add($name);

        // Email
        $email = new Text('email',[
            "placeholder" => "이메일",
        ]);
        $email->setLabel('E-Mail');
        $email->setFilters('email');
        $email->addValidators([
            new PresenceOf([
                'message' => 'The e-mail is required',
            	'cancelOnFail' => true
            ]),
            new Email([
                'message' => 'The e-mail is not valid',
            	'cancelOnFail' => true
            ])
        ]);
        $this->add($email);

        // Password
        $password = new Password('password');
        $password->setLabel('Password');
        $password->addValidators([
            new PresenceOf([
                'message' => '패스워드를 넣어주세요.',
            	'cancelOnFail' => true
            ]),
        	new Regex([
        		'pattern' => '/^.*(?=^.{8,15}$)(?=.*\d)(?=.*[a-zA-Z])(?=.*[!@#$%^&+=]).*$/',
        		'message' => '영어 대/소문자, 숫자 및 특수문자를 조합하여 비밀번호 8자리 15자리 이하로 만들어주세요.',
        		'cancelOnFail' => true
        	]),
            new Confirmation([
                'message' => '비밀번호와 일치하지 않습니다.',
                'with' => 'confirmPassword',
            	'cancelOnFail' => true
            ])
        ]);
        $this->add($password);

        // Confirm Password
        $confirmPassword = new Password('confirmPassword');
        $confirmPassword->setLabel('Confirm Password');
        $confirmPassword->addValidators([
            new PresenceOf([
                'message' => '위에 패스워드와 동일하게 넣어주세요.'
            ])
        ]);
        $this->add($confirmPassword);
        
        // Remember
        $terms = new Check('terms', [
            'value' => 'yes'
        ]);
        $terms->setLabel('Accept terms and conditions');
        $terms->addValidator(new Identical([
            'value' => 'yes',
            'message' => 'Terms and conditions must be accepted'
        ]));
        $this->add($terms);

        // CSRF
        $csrf = new Hidden('csrf');
        $csrf->addValidator(new Identical([
            'value' => $this->security->getSessionToken(),
        	'message' => 'CSRF validation failed'
        ]));
        $csrf->clear();
        $this->add($csrf);

        // Sign Up
        $this->add(new Submit('Sign Up', [
            'class' => 'btn btn-success'
        ]));
    }

    /**
     * Prints messages for a specific element
     */
    public function messages($name)
    {
        if ($this->hasMessagesFor($name)) {
            foreach ($this->getMessagesFor($name) as $message) {
                $this->flash->error($message);
            }
        }
    }
}
