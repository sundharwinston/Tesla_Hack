<?php

if (version_compare(PHP_VERSION, '5.1.2', '>=')) {

    //SPL autoloading was introduced in PHP 5.1.2

    if (version_compare(PHP_VERSION, '5.3.0', '>=')) {

        spl_autoload_register('ClassAutoLoad', true, true);

    } else {

        spl_autoload_register('ClassAutoLoad');

    }

} else {

    /**

     * Fall back to traditional autoload for old PHP versions

     * @param string $class The name of the class to load

     */

    function __autoload($class) 

    {

        ClassAutoLoad($class);

    }

}



function ClassAutoLoad($class)

{

    $class = strtolower($class);

    $classpath = 'class.'. $class .'.php';

    $dirItr    = new RecursiveDirectoryIterator(dirname(__DIR__),RecursiveDirectoryIterator::SKIP_DOTS);

    $filterItr = new RecursiveDirectoryFilterIterator($dirItr);

    $itr       = new RecursiveIteratorIterator($filterItr, RecursiveIteratorIterator::SELF_FIRST);

    foreach ($itr as $classdir) {

        if ( file_exists($classdir."/".$classpath)) {

            require_once $classdir."/".$classpath;

        }

    }

}



class RecursiveDirectoryFilterIterator extends RecursiveFilterIterator {

 public function __construct(Iterator $iter)

    {

        parent::__construct($iter);

    }

    public function accept() {

        $file = $this->getInnerIterator()->current();

        return is_dir($file) && preg_match('/classes|phpmailer/i', $file);;

    }



}

?>