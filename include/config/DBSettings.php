<?php
	class DatabaseSettings{
		var $settings;
		function getSettings()
		{
			$settings['dbhost'] = 'localhost';
			$settings['dbname'] = 'bpapp';
			$settings['dbusername'] = 'root';
			$settings['dbpassword'] = 'lee7578';

			return $settings;
		}
	}
?>
