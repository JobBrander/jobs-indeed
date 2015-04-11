<?php namespace JobBrander\Jobs\Client\Providers\Test;

use JobBrander\Jobs\Client\Providers\Indeed;
use Mockery as m;

class IndeedTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->client = new Indeed([
            'publisherId' => '3806336598146294',
            'version' => 2,
            'highlight' => 0,
        ]);
    }

    public function testItWillProvideEmptyParameters()
    {
        $parameters = $this->client->getParameters();

        $this->assertEmpty($parameters);
        $this->assertTrue(is_array($parameters));
    }

    public function testItCanConnect()
    {
        $listings = ['results' => [
            ['jobtitle' => uniqid(), 'company' => uniqid()],
        ]];

        $this->client->setKeyword('project manager')
            ->setCity('Chicago')
            ->setState('IL');

        $response = m::mock('GuzzleHttp\Message\Response');
        $response->shouldReceive($this->client->getFormat())->once()->andReturn($listings);

        $http = m::mock('GuzzleHttp\Client');
        $http->shouldReceive(strtolower($this->client->getVerb()))
            ->with($this->client->getUrl(), $this->client->getHttpClientOptions())
            ->once()
            ->andReturn($response);
        $this->client->setClient($http);

        $results = $this->client->getJobs();
    }
}
