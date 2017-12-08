<?php

class ChangePasswordDockerMailserverPlugin extends \RainLoop\Plugins\AbstractPlugin
{
	public function Init()
	{
		$this->addHook('main.fabrica', 'MainFabrica');
	}

	/**
	 * @param string $sName
	 * @param mixed $oProvider
	 */
	public function MainFabrica($sName, &$oProvider)
	{
		switch ($sName)
		{
			case 'change-password':

				include_once __DIR__.'/ChangePasswordDockerMailserverDriver.php';

				$oProvider = new ChangePasswordDockerMailserverDriver();
				$oProvider->SetAllowedEmails(\strtolower(\trim($this->Config()->Get('plugin', 'allowed_emails', ''))));
                $oProvider->SetManagementEndpoint(\strtolower(\trim($this->Config()->Get('plugin', 'management_endpoint', ''))));

				break;
		}
	}

	/**
	 * @return array
	 */
	public function configMapping()
	{
		return array(
			\RainLoop\Plugins\Property::NewInstance('allowed_emails')->SetLabel('Allowed emails')
				->SetType(\RainLoop\Enumerations\PluginPropertyType::STRING_TEXT)
				->SetDescription('Allowed emails, space as delimiter, wildcard supported. Example: user1@domain1.net user2@domain1.net *@domain2.net')
				->SetDefaultValue('*'),

            \Rainloop\Plugins\Property::NewInstance('management_endpoint')
                ->SetLabel('Management API URL')
                ->SetDescription('The endpoint of the docker-mailserver-management api (Example: management:3000')
                ->SetDefaultValue('management:3000')
		);
	}
}
