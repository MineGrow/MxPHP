<?php 

$this->get('v1/user/info', function(Core\App $app) {
	return 'Hello Get Router';
});

$this->post('v1/user/info', function(Core\App $app) {
	return 'Hello post Router';
});

$this->put('v1/user/info', function(Core\App $app) {
	return 'Hello put Router';
});

$this->delete('v1/user/info', function(Core\App $app) {
	return 'Hello delete Router';
});