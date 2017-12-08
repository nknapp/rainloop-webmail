<?php

class ChangePasswordDockerMailserverDriver implements \RainLoop\Providers\ChangePassword\ChangePasswordInterface
{
	/**
	 * @var string
	 */
	private $sAllowedEmails = '';

    /**
     * @var string
     */
    private $sManagementEndpoint = '';

    /**
	 * @param string $sAllowedEmails
	 *
	 * @return \ChangePasswordDockerMailserverDriver
	 */
	public function SetAllowedEmails($sAllowedEmails)
	{
		$this->sAllowedEmails = $sAllowedEmails;
		return $this;
	}

    /**
     * @param string $sManagementEndpoint
     *
     * @return \ChangePasswordDockerMailserverDriver
     */
    public function SetManagementEndpoint($sManagementEndpoint)
    {
        $this->sManagementEndpoint = $sManagementEndpoint;
        return $this;
    }

    /**
	 * @param \RainLoop\Model\Account $oAccount
	 *
	 * @return bool
	 */
	public function PasswordChangePossibility($oAccount)
	{
		return $oAccount && $oAccount->Email() &&
			\RainLoop\Plugins\Helper::ValidateWildcardValues($oAccount->Email(), $this->sAllowedEmails);
	}

	/**
	 * @param \RainLoop\Model\Account $oAccount
     * @param string $sPrevPassword
	 * @param string $sNewPassword
	 *
	 * @return bool
	 */
	public function ChangePassword(\RainLoop\Account $oAccount, $sPrevPassword, $sNewPassword)
	{
		$data = array(
            "oldPassword" => $sPrevPassword,
            "newPassword" => $sNewPassword
        );
        $data_string = json_encode($data);

        $endpoint = $this->sManagementEndpoint;
        $username = $oAccount->Login();

        $ch = curl_init($endpoint . "/users/" . urlencode($username));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string))
        );

        $bResult = curl_exec($ch);
        curl_close($ch);

		return $bResult;
	}
}
