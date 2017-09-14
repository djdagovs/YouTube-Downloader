<?php

namespace YoutubeDownloader\Tests\Unit\Container;

use YoutubeDownloader\Container\SimpleContainer;
use YoutubeDownloader\Tests\Fixture\TestCase;
use YoutubeDownloader\Tests\Fixture\Container\Psr11ContainerAdapter;

class SimpleContainerTest extends TestCase
{
	/**
	 * @test SimpleContainer implements Container
	 */
	public function implementsContainer()
	{
		$container = new SimpleContainer();

		$this->assertInstanceOf('\\YoutubeDownloader\\Container\\Container', $container);
	}

	/**
	 * @test SimpleContainer is compatible with Psr\Container\ContainerInterface
	 */
	public function isPsr11Compatible()
	{
		$container = new SimpleContainer();

		$adapter = new Psr11ContainerAdapter($container);

		$this->assertInstanceOf('\\Psr\\Container\\ContainerInterface', $adapter);
		$this->assertInstanceOf('\\YoutubeDownloader\\Container\\Container', $adapter);
	}

	/**
	 * @test set(), has() and get()
	 * @dataProvider GetterSetterProvider
	 */
	public function testSetterAndGetterThrowsExceptionWithoutClosure($id, $value)
	{
		$container = new SimpleContainer();

		$this->expectException('\\YoutubeDownloader\\Container\\ContainerException');
		$this->expectExceptionMessage('Second argument ($value) must be a Closure or a string as alias to an existing entry.');

		$container->set($id, $value);
	}

	/**
	 * @test set(), has() and get()
	 * @dataProvider GetterSetterProvider
	 */
	public function testSetterAndGetter($id, $value)
	{
		$container = new SimpleContainer();

		$closure = function($c) use ($value) {
			return $value;
		};

		$container->set($id, $closure);

		$this->assertTrue($container->has($id));
		$this->assertSame($value, $container->get($id));
	}

	/**
	 * @test set(), has() and get()
	 * @dataProvider GetterSetterProvider
	 */
	public function testSetterAndGetterAlias($id, $value)
	{
		$container = new SimpleContainer();

		$closure = function($c) use ($value) {
			return $value;
		};

		$container->set($id, $closure);
		$container->set($id . '-alias', $id);

		$this->assertTrue($container->has($id.'-alias'));
		$this->assertSame($value, $container->get($id.'-alias'));
	}

	/**
	 * GetterSetterProvider
	 */
	public function GetterSetterProvider()
	{
		return [
			['null', null],
			['true', true],
			['false', false],
			['int', 123456789],
			['float', 1234.56789],
			['string', 'string'],
			['array', ['array']],
			['object', new \stdClass],
		];
	}

	/**
	 * @test SimpleContainer throws NotFoundException
	 */
	public function getThrowsNotFoundException()
	{
		$container = new SimpleContainer();

		$this->expectException('\\YoutubeDownloader\\Container\\NotFoundException');
		$this->expectExceptionMessage('Entry "foo" don\'t exists in the container');

		$container->get('foo');
	}
}
