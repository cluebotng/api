<?PHP

namespace ApiInterface;

ini_set('display_errors', 'Off');
error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('America/New_York');
ini_set('user_agent', 'ClueBot/2.0 (ClueBot NG API Interface)');

require_once('vendor/autoload.php');
require_once('settings.php');
require_once('includes/Helpers.php');
require_once('includes/Module.php');
foreach (glob('modules/*.module.php') as $module) {
    require_once($module);
}

function cleanup_on_exit()
{
    global $mw_mysql;
    @mysql_close($mw_mysql);
}
register_shutdown_function('\\ApiInterface\\cleanup_on_exit');

header('Content-Type: application/json');
if (array_key_exists('action', $_REQUEST) && ($module = ApiModule::find($_REQUEST['action']))) {
    $mw_mysql = @mysqli_connect($mw_mysql_host, $mw_mysql_user, $mw_mysql_pass, $mw_mysql_schema, $mw_mysql_port);
    if (!$mw_mysql) {
        header('HTTP/1.1 500 Internal Server Error');
        die(json_encode(array(
            'error' => 'db_error',
            'error_message' => 'Could not connect to the database server',
        ), JSON_PRETTY_PRINT));
    }
    die($module->execute());
} else {
    header('HTTP/1.1 404 Not Found');
    die(json_encode(null, JSON_PRETTY_PRINT));
}
