<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Error Messages Language Lines
|--------------------------------------------------------------------------
|
| The following language lines are used for error messages throughout
| the application. These should be used instead of hardcoded strings.
|
*/

return [
	// HTTP Errors
	'http' => [
		'400' => [
			'title' => 'Bad Request',
			'description' => 'The request could not be understood or was missing required parameters.',
		],
		'401' => [
			'title' => 'Unauthorized',
			'description' => 'Authentication is required to access this resource.',
		],
		'403' => [
			'title' => 'Forbidden',
			'description' => 'You do not have permission to access this resource.',
		],
		'404' => [
			'title' => 'Not Found',
			'description' => 'The requested resource could not be found.',
		],
		'405' => [
			'title' => 'Method Not Allowed',
			'description' => 'The request method is not supported for this resource.',
		],
		'408' => [
			'title' => 'Request Timeout',
			'description' => 'The request took too long to process.',
		],
		'419' => [
			'title' => 'Page Expired',
			'description' => 'Your session has expired. Please refresh the page and try again.',
		],
		'429' => [
			'title' => 'Too Many Requests',
			'description' => 'You have made too many requests. Please slow down and try again later.',
		],
		'500' => [
			'title' => 'Internal Server Error',
			'description' => 'An unexpected error occurred. Please try again later.',
		],
		'503' => [
			'title' => 'Service Unavailable',
			'description' => 'The service is temporarily unavailable. Please try again later.',
		],
	],

	// Generic Errors
	'generic' => [
		'unknown' => 'An unknown error occurred.',
		'try_again' => 'Please try again later.',
		'contact_support' => 'If the problem persists, please contact support.',
		'invalid_input' => 'Invalid input provided.',
		'invalid_request' => 'Invalid request.',
		'operation_failed' => 'Operation failed.',
		'not_found' => 'Resource not found.',
		'unauthorized' => 'Unauthorized access.',
		'forbidden' => 'Access forbidden.',
		'validation_error' => 'Validation error occurred.',
	],

	// Database Errors
	'database' => [
		'connection' => 'Database connection failed.',
		'query' => 'Database query failed.',
		'transaction' => 'Database transaction failed.',
		'unique_violation' => 'A record with this value already exists.',
		'foreign_key' => 'This record cannot be deleted because it is referenced by other records.',
	],

	// File Errors
	'file' => [
		'not_found' => 'File not found.',
		'upload_failed' => 'File upload failed.',
		'invalid_type' => 'Invalid file type.',
		'too_large' => 'File size exceeds the maximum allowed (:max MB).',
		'read_failed' => 'Failed to read file.',
		'write_failed' => 'Failed to write file.',
		'delete_failed' => 'Failed to delete file.',
	],

	// Network Errors
	'network' => [
		'timeout' => 'Request timed out.',
		'connection' => 'Connection failed.',
		'dns' => 'DNS lookup failed.',
		'ssl' => 'SSL certificate verification failed.',
		'unavailable' => 'Service is currently unavailable.',
	],

	// Validation Errors
	'validation' => [
		'required' => 'This field is required.',
		'invalid' => 'Invalid value provided.',
		'too_short' => 'Value is too short (minimum :min characters).',
		'too_long' => 'Value is too long (maximum :max characters).',
		'invalid_email' => 'Invalid email address.',
		'invalid_url' => 'Invalid URL.',
		'invalid_date' => 'Invalid date format.',
		'invalid_number' => 'Invalid number.',
		'min_value' => 'Value must be at least :min.',
		'max_value' => 'Value must not exceed :max.',
	],

	// Permission Errors
	'permission' => [
		'denied' => 'Permission denied.',
		'insufficient' => 'You do not have sufficient permissions to perform this action.',
		'admin_required' => 'Administrator privileges required.',
		'moderator_required' => 'Moderator privileges required.',
		'owner_required' => 'Only the owner can perform this action.',
	],

	// Rate Limiting Errors
	'rate_limit' => [
		'exceeded' => 'Too many requests. Please try again in :seconds seconds.',
		'login_attempts' => 'Too many login attempts. Please try again in :minutes minutes.',
		'api_limit' => 'API rate limit exceeded. Please try again later.',
	],
];