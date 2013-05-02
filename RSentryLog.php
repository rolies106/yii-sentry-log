<?php
/**
 * RSentryLog class file.
 *
 * @author Rolies Deby <rolies106@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008-2012 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * RSentryLog records log messages to sentry server.
 *
 * @author Rolies Deby <rolies106@gmail.com>
 * @version $Id: CFileLogRoute.php 3426 2011-10-25 00:01:09Z alexander.makarow $
 * @package system.logging
 * @since 1.0
 */
class RSentryLog extends CLogRoute
{
    const DEBUG = 10;
    const INFO = 20;
    const WARNING = 30;
    const ERROR = 40;

    /**
     * @var string Sentry DSN value
     */
    public $dsn;

    /**
     * @var class Sentry stored connection
     */
    protected $_client;

    /**
     * @var string Logger identifier
     */
    protected $logger = 'php';

    /**
     * Initializes the connection.
     */
    public function init()
    {
        parent::init();
        
        if(!class_exists('Raven_Autoloader', false)) {
            # Turn off our amazing library autoload
            spl_autoload_unregister(array('YiiBase','autoload'));

            # Include request library
            include(dirname(__FILE__) . '/lib/Raven/Autoloader.php');

            # Run request autoloader
            Raven_Autoloader::register();

            # Give back the power to Yii
            spl_autoload_register(array('YiiBase','autoload'));
        }

        if($this->_client===null)
            $this->_client = new Raven_Client($this->dsn, array('logger' => $this->logger));
    }

    /**
     * Send log messages to Sentry.
     * @param array $logs list of log messages
     * @return Sentry event identifier
     */
    protected function processLogs($logs)
    {
        if (defined('YII_DEBUG') && YII_DEBUG === true) {
            return false;
        }
           
        foreach($logs as $log) {
            $format = explode("\n", $log[0]);
            $title = strip_tags($format[0]);
            $sentryEventId = $this->_client->getIdent($this->_client->captureMessage($title, array('referrer' => Yii::app()->request->urlReferrer), $log[1], false));

            return $sentryEventId;
        }
    }
}
