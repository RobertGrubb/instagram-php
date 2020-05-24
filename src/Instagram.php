<?php

/**
 * Core Files
 */
require_once dirname(__FILE__) . '/Instagram/Scraper.php';

/**
 * Models
 */
require_once dirname(__FILE__) . '/Instagram/Models/Account.php';

/**
 * Validations
 */
require_once dirname(__FILE__) . '/Instagram/Validations/AccountValidation.php';

/**
 * Resources
 */
require_once dirname(__FILE__) . '/Instagram/Resources/Endpoints.php';

/**
 * Libraries
 */
require_once dirname(__FILE__) . '/Instagram/Libraries/Request.php';
require_once dirname(__FILE__) . '/Instagram/Libraries/DOMResponse.php';
require_once dirname(__FILE__) . '/Instagram/Libraries/JSONResponse.php';

/**
 * Exceptions
 */
require_once dirname(__FILE__) . '/Instagram/Exceptions/InstagramEncodedException.php';
