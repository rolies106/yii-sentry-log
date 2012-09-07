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
 * @author Qiang Xue <qiang.xue@gmail.com>
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
	 * Initializes the connection.
	 */
	public function init()
	{
		parent::init();
		Raven_Autoloader::register();

		if($this->_client===null)
			$this->_client = new Raven_Client($this->dsn);
	}

	/**
	 * Send log messages to Sentry.
	 * @param array $logs list of log messages
	 */
	protected function processLogs($logs)
	{		
		foreach($logs as $log) {
			if ($log[1] == 'error') {
				$level = self::ERROR;
			} else if ($log[1] == 'warning') {
				$level = self::WARNING;
			} else if ($log[1] == 'info') {
				$level = self::INFO;
			} else if ($log[1] == 'trace') {
				$level = self::DEBUG;
			}

			$title = ucwords($log[1]) . ' - ' . strtoupper($log[2]);
			$this->_client->captureMessage($title, array(), $level, true);
		}
	}
}