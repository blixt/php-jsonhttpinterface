# PHP JSON via HTTP interface

## Information

The `JsonHttpInterface` class wraps a class instance and makes all its public
methods callable through HTTP, accepting named parameters (JSON encoded) and
returning the result JSON encoded.

## Examples

Assume this is **myservice.php**:

    <?php
    require('JsonHttpInterface.php');

    class MyService {
        function add($a, $b) {
            return $a + $b;
        }
    }

    $svc = new MyService();
    $jhi = new JsonHttpInterface($svc);
    $jhi->exec();
    ?>

Now make an HTTP call to `myservice.php/add?a=1300&b=37` and you will get this
response (indented for readability):

    {
        "status": "success",
        "response": 1337
    }

## MIT license

This project is licensed under an [MIT license][].

Copyright © 2011 Andreas Blixt (<andreas@blixt.org>)

[MIT license]: http://www.opensource.org/licenses/mit-license.php
