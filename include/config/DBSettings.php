<?php
	class DatabaseSettings{
		var $settings;
		function getSettings()
		{
			$settings['dbhost'] = '192.168.0.7';
			$settings['dbname'] = 'bpapp';
			$settings['dbusername'] = 'root';
			$settings['dbpassword'] = 'lee7578';
			
			return $settings;
		}
	}
?>