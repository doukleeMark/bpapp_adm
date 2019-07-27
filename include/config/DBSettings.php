<?php
	class DatabaseSettings{
		var $settings;
		function getSettings()
		{
			$settings['dbhost'] = '172.17.0.3';
			$settings['dbname'] = 'bpapp';
			$settings['dbusername'] = 'root';
			$settings['dbpassword'] = 'lee7578';

			return $settings;
		}
	}
?>
