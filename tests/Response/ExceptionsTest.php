<?php

namespace InstagramAPI\Tests\Response;

use InstagramAPI\Client;
use InstagramAPI\Exception\AccountDisabledException;
use InstagramAPI\Exception\ChallengeRequiredException;
use InstagramAPI\Exception\CheckpointRequiredException;
use InstagramAPI\Exception\ConsentRequiredException;
use InstagramAPI\Exception\FeedbackRequiredException;
use InstagramAPI\Exception\IncorrectPasswordException;
use InstagramAPI\Exception\InvalidSmsCodeException;
use InstagramAPI\Exception\InvalidUserException;
use InstagramAPI\Exception\LoginRequiredException;
use InstagramAPI\Exception\SentryBlockException;
use InstagramAPI\Exception\ServerMessageThrower;
use InstagramAPI\Response\GenericResponse;
use PHPUnit\Framework\TestCase;

class ExceptionsTest extends TestCase
{
    /**
     * @param string $json
     *
     * @return GenericResponse
     */
    protected function _makeResponse(
        $json)
    {
        return new GenericResponse(Client::api_body_decode($json));
    }

    public function testLoginRequiredException()
    {
        $this->expectException(LoginRequiredException::class);
        $this->expectExceptionMessage('Login required');
        $response = $this->_makeResponse('{"message":"login_required", "logout_reason": 2, "status": "fail"}');
        ServerMessageThrower::autoThrow(null, $response->getMessage(), $response);
    }

    public function testFeedbackRequiredException()
    {
        $this->expectException(FeedbackRequiredException::class);
        $this->expectExceptionMessage('Feedback required');
        $response = $this->_makeResponse('{"message":"feedback_required","spam":true,"feedback_title":"You\u2019re Temporarily Blocked","feedback_message":"It looks like you were misusing this feature by going too fast. You\u2019ve been blocked from using it.\n\nLearn more about blocks in the Help Center. We restrict certain content and actions to protect our community. Tell us if you think we made a mistake.","feedback_url":"WUT","feedback_appeal_label":"Report problem","feedback_ignore_label":"OK","feedback_action":"report_problem","status":"fail"}');
        ServerMessageThrower::autoThrow(null, $response->getMessage(), $response);
    }

    public function testConsentRequiredException()
    {
        $this->expectException(ConsentRequiredException::class);
        $this->expectExceptionMessage('Consent required');
        $response = $this->_makeResponse('{"message":"consent_required","consent_data":{"headline":"Updates to Our Terms and Data Policy","content":"We\'ve updated our Terms and made some changes to our Data Policy. Please take a moment to review these changes and let us know that you agree to them.\n\nYou need to finish reviewing this information before you can use Instagram.","button_text":"Review Now"},"status":"fail"}');
        ServerMessageThrower::autoThrow(null, $response->getMessage(), $response);
    }

    public function testCheckpointRequiredException()
    {
        $this->expectException(CheckpointRequiredException::class);
        $this->expectExceptionMessage('Checkpoint required');
        $response = $this->_makeResponse('{"message":"checkpoint_required","checkpoint_url":"WUT","lock":true,"status":"fail","error_type":"checkpoint_challenge_required"}');
        ServerMessageThrower::autoThrow(null, $response->getMessage(), $response);
    }

    public function testChallengeRequiredException()
    {
        $this->expectException(ChallengeRequiredException::class);
        $this->expectExceptionMessage('Challenge required');
        $response = $this->_makeResponse('{"message":"challenge_required","challenge":{"url":"https://i.instagram.com/challenge/","api_path":"/challenge/","hide_webview_header":false,"lock":true,"logout":false,"native_flow":true},"status":"fail"}');
        ServerMessageThrower::autoThrow(null, $response->getMessage(), $response);
    }

    public function testIncorrectPasswordException()
    {
        $this->expectException(IncorrectPasswordException::class);
        $this->expectExceptionMessageRegExp('/password.*incorrect/i');
        $response = $this->_makeResponse('{"message":"The password you entered is incorrect. Please try again.","invalid_credentials":true,"error_title":"Incorrect password for WUT","buttons":[{"title":"Try Again","action":"dismiss"}],"status":"fail","error_type":"bad_password"}');
        ServerMessageThrower::autoThrow(null, $response->getMessage(), $response);
    }

    public function testAccountDisabledException()
    {
        $this->expectException(AccountDisabledException::class);
        $this->expectExceptionMessageRegExp('/account.*disabled/i');
        $response = $this->_makeResponse('{"message":"Your account has been disabled for violating our terms. Learn how you may be able to restore your account."}');
        ServerMessageThrower::autoThrow(null, $response->getMessage(), $response);
    }

    public function testInvalidUserException()
    {
        $this->expectException(InvalidUserException::class);
        $this->expectExceptionMessageRegExp('/check.*username/i');
        $response = $this->_makeResponse('{"message":"The username you entered doesn\'t appear to belong to an account. Please check your username and try again.","invalid_credentials":true,"error_title":"Incorrect Username","buttons":[{"title":"Try Again","action":"dismiss"}],"status":"fail","error_type":"invalid_user"}');
        ServerMessageThrower::autoThrow(null, $response->getMessage(), $response);
    }

    public function testSentryBlockException()
    {
        $this->expectException(SentryBlockException::class);
        $this->expectExceptionMessageRegExp('/problem.*request/i');
        $response = $this->_makeResponse('{"message":"Sorry, there was a problem with your request.","status":"fail","error_type":"sentry_block"}');
        ServerMessageThrower::autoThrow(null, $response->getMessage(), $response);
    }

    public function testInvalidSmsCodeException()
    {
        $this->expectException(InvalidSmsCodeException::class);
        $this->expectExceptionMessageRegExp('/check.*code/i');
        $response = $this->_makeResponse('{"message":"Please check the security code we sent you and try again.","status":"fail","error_type":"sms_code_validation_code_invalid"}');
        ServerMessageThrower::autoThrow(null, $response->getMessage(), $response);
    }
}
