<?php
// composer install
// ./vendor/bin/phpunit --bootstrap vendor/autoload.php tests/MainTest

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// $this->assertTrue($res ? $res : $mail->getErrorInfo());
// $this->assertGreaterThan(15, $daysLeft);
// $this->assertSame("1 personne", $txt);

final class MainTest extends TestCase
{
    /**
     * @test
     * Check we can access the Docker API
     */
    public function testDocker()
    {
        $url  = 'http://v1.24/containers/json';

        if(!$ch = curl_init($url)) {
            $this->assertEmpty('You have to enable curl');
        }
        $stderr = fopen('php://temp', 'w+');

        curl_setopt_array($ch, array(
            CURLOPT_UNIX_SOCKET_PATH => '/var/run/docker.sock',
            CURLOPT_RETURNTRANSFER   => true,
            CURLOPT_FOLLOWLOCATION   => true,
            CURLOPT_CONNECTTIMEOUT   => 10,
            CURLOPT_TIMEOUT          => 50,
            CURLOPT_VERBOSE          => true,
            CURLOPT_STDERR           => $stderr
          //  CURLOPT_POST           => true,
           // CURLOPT_POSTFIELDS     => http_build_query($args)
        ));
        $result = curl_exec($ch);
        $status = curl_getinfo($ch)['http_code'];

        // Check that we get a result
        if($result === false) {
            rewind($stderr);
            $log = stream_get_contents($stderr);

             $this->assertFalse(sprintf("cUrl error (#%d): %s\n%s\n",
                curl_errno($ch),
                curl_error($ch),
                $log
            ));
        }

        // Check the status of the response
        $this->assertSame($status, 200);
        curl_close($ch);
    }
}