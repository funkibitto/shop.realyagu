<?php
namespace Realyagu\Controllers;
use Realyagu\Models\EmailConfirmations;
use Realyagu\Models\ResetPasswords;
/**
 * UserStatusController
 * email 인증, password 변경
 */
class UserStatusController extends ControllerBase
{
    public function initialize()
    {
        if ($this->session->has('auth-identity')) {
            $this->view->setTemplateBefore('private');
        }
    }
    public function indexAction()
    {
    }
    /**
     * Confirms an e-mail, if the user must change thier password then changes it
     */
    public function confirmEmailAction()
    {
        $code = $this->dispatcher->getParam('code');
        $confirmation = EmailConfirmations::findFirstByCode($code);
        if (!$confirmation) {
            return $this->dispatcher->forward([
                'controller' => 'index',
                'action' => 'index'
            ]);
        }
        if ($confirmation->confirmed != 'N') {
            return $this->dispatcher->forward([
                'controller' => 'session',
                'action' => 'login'
            ]);
        }
        $confirmation->confirmed = 'Y';
        $confirmation->user->status = $this->getDI()->get('config')->userStatus->live;;
        /**
         * Change the confirmation to 'confirmed' and update the user to 'active'
         */
        if (!$confirmation->save()) {
            foreach ($confirmation->getMessages() as $message) {
                $this->flash->error($message);
            }
            return $this->dispatcher->forward([
                'controller' => 'index',
                'action' => 'index'
            ]);
        }
        /**
         * Identify the user in the application
         */
        $this->auth->authUserById($confirmation->user->Id);
        /**
         * Check if the user must change his/her password
         */
        if ($confirmation->user->mustChangePassword == 'Y') {
            $this->flash->success('이메일 인증을 완료 하셨습니다. 패스워드 변경을 하세요.');
            return $this->dispatcher->forward([
                'controller' => 'users',
                'action' => 'changePassword'
            ]);
        }
        $this->flash->success('이메일 인증을 완료 하셨습니다.');
        return $this->dispatcher->forward([
            'controller' => 'index',
            'action' => 'index'
        ]);
    }
    public function resetPasswordAction()
    {
        $code = $this->dispatcher->getParam('code');
        $resetPassword = ResetPasswords::findFirstByCode($code);
        if (!$resetPassword) {
            return $this->dispatcher->forward([
                'controller' => 'index',
                'action' => 'index'
            ]);
        }
        if ($resetPassword->reset != 'N') {
            return $this->dispatcher->forward([
                'controller' => 'session',
                'action' => 'login'
            ]);
        }
        $resetPassword->reset = 'Y';
        /**
         * Change the confirmation to 'reset'
         */
        if (!$resetPassword->save()) {
            foreach ($resetPassword->getMessages() as $message) {
                $this->flash->error($message);
            }
            return $this->dispatcher->forward([
                'controller' => 'index',
                'action' => 'index'
            ]);
        }
        /**
         * Identify the user in the application
         */
        $this->auth->authUserById($resetPassword->usersId);
        $this->flash->success('Please reset your password');
        return $this->dispatcher->forward([
            'controller' => 'users',
            'action' => 'changePassword'
        ]);
    }
}