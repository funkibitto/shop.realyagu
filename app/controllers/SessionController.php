<?php
namespace Realyagu\Controllers;

use Realyagu\Forms\SignUpForm;
use Realyagu\Forms\SignUpShopForm;
use Realyagu\Models\Users;
use Realyagu\Models\Shops;
use Realyagu\Models\EmailConfirmations;
use Phalcon\Mvc\Model\Transaction\Failed as TxFailed;
use Phalcon\Mvc\Model\Transaction\Manager as TxManager;
/*
 * 회원가입, 로그인, session, 기타등등
 *  
 */

class SessionController extends ControllerBase
{	
	public function initialize()
	{
		$this->tag->setTitle('SignUp');
		parent::initialize();
	}
	
	public function indexAction()
	{

	}
	
	/*
	 *  test
	 */
	public function socketAction()
	{
	
	}
		
	/**
	 * 회원가입
	 */
	public function signUpAction()
	{
		$form = new SignUpForm();
		if ($this->request->isPost() && $this->security->checkToken($this->security->getTokenKey(), $this->request->getPost('csrf'))) {
			if ($form->isValid($this->request->getPost()) != false) {
				//관리자등록
				$user = new Users([
						'userName' => $this->request->getPost('userName', 'alphanum'),
						'name' => $this->request->getPost('name', 'striptags'),
						'email' => $this->request->getPost('email', 'email'),
						'password' => $this->security->hash($this->request->getPost('password')),
						'confirmPassword' => $this->request->getPost('confirmPassword'),
						'grade' =>  $this->getDI()->get('config')->grade->administrator, //관리자 등급
						'phoneNumber' =>   $this->request->getPost('phoneNumber'),
				]);

				if ($user->save()) {
					// Only send the confirmation email if emails are turned on in the config
					if ($this->getDI()->get('config')->useMail && $user->status == 0) {
						$emailConfirmation = new EmailConfirmations();
						$emailConfirmation->usersId = $user->id;
						try {
							if (!$emailConfirmation->save()) {
                                $this->flash->success('인증 메일 발송에 실패 하였습니다.');
							}
						} catch (\PDOException $e) {
							echo get_class($e), ": ", $e->getMessage(), "\n";
							echo " File=", $e->getFile(), "\n";
							echo " Line=", $e->getLine(), "\n";
							echo $e->getTraceAsString();
						}
					}
					
					
// 					return $this->dispatcher->forward([
// 							'controller' => 'index',
// 							'action' => 'index'
// 					]);
				}
				
				$this->flash->error($user->getMessages());
			}
		}
		$this->view->form = $form;
	}
	
	/**
	 * 샵 등록 
	 */
	public function signUpShopAction()
	{
		$form = new SignUpShopForm();
		//샵등록
		$shop = new Shops([
				'userId' => $user->id,
				'name' => $this->request->getPost('shopName', 'striptags'),
				'site' => $this->request->getPost('site', 'striptags'),
				'businessNumber' => $this->request->getPost('businessNumber'),
				'businessConditions' => $this->request->getPost('businessConditions', 'striptags'),
				'businessType' => $this->request->getPost('businessType', 'striptags'),
				'phoneNumber' => $this->request->getPost('phoneNumber', 'striptags'),
				'zipCode' => $this->request->getPost('zipCode', 'striptags'),
				'address1' => $this->request->getPost('address1', 'striptags'),
				'address2' => $this->request->getPost('address2', 'striptags')
		]);

		if ($shop->save() == false) {
			$transaction->rollback(
					"샵등록에 실패 하였습니다."
					);
		} else {
			return $this->dispatcher->forward([
					'controller' => 'index',
					'action' => 'index'
			]);
		}
		
		//users shopId를 넣어주자.
		if ($user->update(['shopId' => $shop->id]) == false) {
			$transaction->rollback(
					"샵고유 번호 저장에 실패 하였습니다."
					);
		} else {
			return $this->dispatcher->forward([
					'controller' => 'index',
					'action' => 'index'
			]);
		}
			
		
	}
}
