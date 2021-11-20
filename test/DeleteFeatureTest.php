<?php

declare(strict_types=1);

namespace Pheature\Test\Crud\Psr7\Toggle;

use Pheature\Core\Toggle\Write\Event\FeatureWasRemoved;
use Pheature\Core\Toggle\Write\Feature;
use Pheature\Core\Toggle\Write\FeatureId;
use Pheature\Core\Toggle\Write\FeatureRepository;
use Pheature\Crud\Psr7\Toggle\DeleteFeature;
use Pheature\Crud\Toggle\Handler\RemoveFeature;
use Pheature\InMemory\Toggle\InMemoryFeatureRepository;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class DeleteFeatureTest extends TestCase
{
    public function testItShouldReturnNotFoundResponseGivenInvalidFeatureId(): void
    {
        $featureRepository = $this->createMock(FeatureRepository::class);
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getAttribute')
            ->with('feature_id')
            ->willReturn(2354356);
        $response = $this->createMock(ResponseInterface::class);
        $responseFactory = $this->createMock(ResponseFactoryInterface::class);
        $responseFactory->expects($this->once())
            ->method('createResponse')
            ->with(404, 'Route Not Found.')
            ->willReturn($response);

        $handler = new DeleteFeature(new RemoveFeature($featureRepository), $responseFactory);
        $handler->handle($request);
    }

    public function testItShouldHandleRequestAndReturnNoContentResponse(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getAttribute')
            ->with('feature_id')
            ->willReturn('some_id');

        $featureRepository = new InMemoryFeatureRepository();
        $featureRepository->save(new Feature(FeatureId::fromString('some_id'), false, []));
        $feature = $featureRepository->get(FeatureId::fromString('some_id'));

        $response = $this->createMock(ResponseInterface::class);
        $responseFactory = $this->createMock(ResponseFactoryInterface::class);
        $responseFactory->expects($this->once())
            ->method('createResponse')
            ->with(204)
            ->willReturn($response);

        $handler = new DeleteFeature(new RemoveFeature($featureRepository), $responseFactory);
        $handler->handle($request);

        $events = $feature->release();
        self::assertCount(1, $events);
        $event = reset($events);
        self::assertInstanceOf(FeatureWasRemoved::class, $event);
    }
}
