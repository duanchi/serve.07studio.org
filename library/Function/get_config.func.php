<?php
function get_config ($_scope = NULL) {
	$_result = Yaf\Registry::get('config');
	if ($_scope != NULL) {
		foreach (explode('.', $_scope) as $scope) {
			$_result = $_result->get($scope);
		}
	}
	return $_result;
}