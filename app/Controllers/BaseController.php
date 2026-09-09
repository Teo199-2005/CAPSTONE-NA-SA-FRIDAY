<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = ['school_year', 'grade_level'];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = service('session');
    }

    /**
     * Send a dompdf document through CodeIgniter's response (fixes raw %PDF text on shared hosting).
     */
    protected function sendPdfInline(\Dompdf\Dompdf $dompdf, string $filename, bool $download = false): ResponseInterface
    {
        $safeName = preg_replace('/[^\w\-\.]+/', '_', basename($filename)) ?: 'document.pdf';
        $pdf      = $dompdf->output();
        $disposition = ($download ? 'attachment' : 'inline') . '; filename="' . $safeName . '"';

        return $this->response
            ->setStatusCode(200)
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', $disposition)
            ->setHeader('Content-Length', (string) strlen($pdf))
            ->setHeader('Cache-Control', 'private, max-age=0, must-revalidate')
            ->setBody($pdf);
    }
}
