<?php
// JSON via HTTP object wrapper class.
// Copyright © 2011 Andreas Blixt <andreas@blixt.org>
// MIT license

class JsonHttpInterface {
    private $instance;

    function __construct($instance) {
        $this->instance = $instance;
    }

    private function getParam($name) {
        if (get_magic_quotes_gpc()) {
            $json = stripslashes($_GET[$name]);
        } else {
            $json = $_GET[$name];
        }
        $value = json_decode('[' . $json . ']');
        return $value[0];
    }
    
    private function hasParam($name) {
        return isset($_GET[$name]);
    }

    function exec() {
        $er = error_reporting(0);
        header('Content-Type: application/json');

        try {
            if (empty($_SERVER['PATH_INFO'])) {
                throw new Exception('Could not extract path info.');
            }

            // Get the specified action (the method to call.)
            $action = substr($_SERVER['PATH_INFO'], 1);
            // Create an array that will hold the arguments in the positional
            // order.
            $args = array();

            // Get a reflection object for the method that will be called.
            $method = new ReflectionMethod(get_class($this->instance), $action);

            // Get the parameters of the method and loop through them.
            $params = $method->getParameters();
            $defaults = array();
            foreach ($params as $p) {
                if ($defaults && !$p->isOptional()) {
                    // Only add default values to the arguments array if they're
                    // needed to keep positional integrity.
                    foreach ($defaults as $value) $args[] = $value;
                    $defaults = array();
                }
                
                // Attempt to get a value for the parameter from the query
                // string and read it as a JSON string.
                $name = $p->getName();
                if ($this->hasParam($name)) {
                    $args[] = $this->getParam($name);
                } else if ($p->isOptional() && $p->isDefaultValueAvailable()) {
                    $defaults[] = $p->getDefaultValue();
                } else {
                    throw new Exception(
                        'Parameter "' . $name . '" is required.');
                }
            }

            // Call the method and store its result.
            $response = $method->invokeArgs($this->instance, $args);
            $status = 'success';
        } catch (Exception $e) {
            $status = 'error';
            $response = array(
                'message' => $e->getMessage(),
                'type' => get_class($e));
        }

        echo json_encode(array(
            'status' => $status,
            'response' => $response));
        
        error_reporting($er);
    }
}
?>
