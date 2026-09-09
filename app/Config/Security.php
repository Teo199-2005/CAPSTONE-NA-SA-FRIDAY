<?php
namespace Config;

use CodeIgniter\Config\BaseConfig;

class Security extends BaseConfig
{
    /**
     * CSRF Protection Method
     * @var string 'cookie' or 'session'
     */
    public string $csrfProtection = 'session';

    /**
     * CSRF Token Randomization — randomize on each request for added security
     */
    public bool $tokenRandomize = true;

    /**
     * CSRF Token Name
     */
    public string $tokenName = 'csrf_test_name';

    /**
     * CSRF Header Name
     */
    public string $headerName = 'csrf_test_name';

    /**
     * CSRF Cookie Name (unused with session-based CSRF, kept for compatibility)
     */
    public string $cookieName = 'csrf_cookie_name';

    /**
     * CSRF Expires (2 hours in seconds)
     */
    public int $expires = 7200;

    /**
     * CSRF Regenerate — regenerate token on every submission
     */
    public bool $regenerate = true;

    /**
     * CSRF Redirect — redirect to previous page with error on failure
     * @see https://codeigniter4.github.io/userguide/libraries/security.html#redirection-on-failure
     */
    public bool $redirect = (ENVIRONMENT === 'production');
}