<?php namespace Micflan\CrudBase;

use \TestCase;
use \Auth;
use \Mockery as m;

class CrudBaseTest extends TestCase {

    /**
     * TODO - test that:
     *      -- Routes are created successfully
     *      -- GETs return intended model
     *      -- Validation works on PUT and POST
     *      -- url_title stays unique
     *      -- Pagination works
     *      -- Everything works with company enabled
     *      -- Everything works with company disabled
     **/




    var $name = null;

    public function __construct($name = 'client') {
        $this->name = $name;
    }

    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testIndex()
    {
        $user = m::mock('User[company_id]');
        Auth::shouldReceive('user')->andReturn($user);
        $response = $this->call('GET', $this->name);
        dd($response->original);

        $this->assertResponseStatus(200);
    }

}
