<?php 
ini_set('session.save_path',realpath(dirname($_SERVER['DOCUMENT_ROOT']) . '/session/temp')); 
ini_set('session.gc_probability', 1); 
session_start();
//If (isset($_SESSION['timeout']) && $_SESSION['timeout'] + 10 * 60 < time()){
//    session_destroy();
//    session_start();
//}
//else {
//    $_SESSION['timeout']=time();
//}
?>
<?php
function is_session_started()
{
    if ( php_sapi_name() !== 'cli' ) {
        if ( version_compare(phpversion(), '5.4.0', '>=') ) {
            return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
        } else {
            return session_id() === '' ? FALSE : TRUE;
        }
    }
    return FALSE;
}
?>