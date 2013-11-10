<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity {

    /**
     * Authenticates a user.
     * The example implementation makes sure if the username and password
     * are both 'demo'.
     * In practical applications, this should be changed to authenticate
     * against some persistent user identity storage (e.g. database).
     * @return boolean whether authentication succeeds.
     */
    public function authenticate() {
        $this->password = sha1($this->password);

        $this->errorCode = self::ERROR_USERNAME_INVALID;
        $model = User::model()->find("Username='$this->username' AND Password='$this->password'");
        if ($model != null) {
            $this->errorCode = self::ERROR_NONE;
            Yii::app()->user->setState('uid', $model->Id);
        }
//
//        $users = array(
//            // username => password
//            'demo' => 'demo',
//            'admin' => 'admin',
//        );
//        if (!isset($users[$this->username]))
//            $this->errorCode = self::ERROR_USERNAME_INVALID;
//        elseif ($users[$this->username] !== $this->password)
//            $this->errorCode = self::ERROR_PASSWORD_INVALID;
//        else
//            $this->errorCode = self::ERROR_NONE;
        return !$this->errorCode;
    }

}

