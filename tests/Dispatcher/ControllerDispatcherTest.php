<?php

namespace Tests\Dispatcher;

use Mockery;
use Framework\Request;
use Tests\TestCase;
use Framework\Dispatcher\ControllerDispatcher;
use Tests\Dispatcher\Fixtures\FooController;
use Framework\Dispatcher\Exceptions\ControllerActionNotFoundException;

class ControllerDispatcherTest extends TestCase
{
    protected $dispatcher;
    protected $requestMock;
    protected $fooController;

    protected function setUp()
    {
        parent::setUp();

        $this->fooController = new FooController();
        $this->dispatcher = new ControllerDispatcher();
        $this->requestMock = Mockery::mock(Request::class);
    }

    /** @test */
    public function it_triggers_the_call_action_controller_method()
    {
        $controllerMock = Mockery::mock(FooController::class);

        $controllerMock->shouldReceive('callAction')
            ->with('index', $this->requestMock)
            ->once();

        $this->dispatcher->dispatch($this->requestMock, $controllerMock, 'index');
    }

    /** @test */
    public function it_throws_if_controller_action_method_does_not_exist()
    {
        $this->expectException(ControllerActionNotFoundException::class);

        $this->dispatcher->dispatch($this->requestMock, $this->fooController, 'store');
    }

    /** @test */
    public function it_returns_the_response_from_controller()
    {
        $data = ['status' => 'success', 'foo' => 'bar'];

        $this->requestMock->shouldReceive('all')
            ->andReturn($data);

        $response = $this->dispatcher->dispatch($this->requestMock, $this->fooController, 'index');

        $this->assertEquals($data, $response->content);
    }
}
