<?php

declare(strict_types=1);

namespace Pheature\Crud\Psr7\Toggle;

use InvalidArgumentException;
use Pheature\Crud\Toggle\Command\AddStrategy;
use Pheature\Crud\Toggle\Command\RemoveStrategy;
use Psr\Http\Message\ServerRequestInterface;
use Webmozart\Assert\Assert;

final class PatchRequest
{
    private const ACTION_ADD_STRATEGY = 'add_strategy';
    private const ACTION_REMOVE_STRATEGY = 'remove_strategy';
    public const ACTIONS = [
        self::ACTION_ADD_STRATEGY,
        self::ACTION_REMOVE_STRATEGY,
    ];
    private string $featureId;
    private string $action;
    /** @var array<string|mixed>|null  */
    private ?array $requestData = null;

    public function __construct(ServerRequestInterface $request)
    {
        $featureId = $request->getAttribute('feature_id');
        Assert::string($featureId);

        /** @var array<string, mixed> $body */
        $body = $request->getParsedBody();
        $action = $body['action'] ?? null;
        if (false === is_string($action)) {
            throw new InvalidArgumentException(
                'The request body must have "action" key filled with one of valid actions.'
            );
        }

        $value = $body['value'] ?? null;
        if (false === is_null($value) && false === is_array($value)) {
            throw new InvalidArgumentException(
                'The request body must have "value" key filled with an array containing at least "strategy_id",'
                . ' and "strategy_type" in some classes.'
            );
        }

        $this->featureId = $featureId;
        $this->action = $action;
        $this->requestData = $value;
    }

    public function addStrategyCommand(): AddStrategy
    {
        Assert::notNull($this->requestData);
        Assert::keyExists($this->requestData, 'strategy_id');
        Assert::keyExists($this->requestData, 'strategy_type');
        Assert::string($this->requestData['strategy_id']);
        Assert::string($this->requestData['strategy_type']);

        return AddStrategy::withIdAndType(
            $this->featureId,
            $this->requestData['strategy_id'],
            $this->requestData['strategy_type']
        );
    }

    public function removeStrategyCommand(): RemoveStrategy
    {
        Assert::notNull($this->requestData);
        Assert::keyExists($this->requestData, 'strategy_id');
        Assert::string($this->requestData['strategy_id']);

        return RemoveStrategy::withFeatureAndStrategyId(
            $this->featureId,
            $this->requestData['strategy_id']
        );
    }

    public function isAddStrategyAction(): bool
    {
        return self::ACTION_ADD_STRATEGY === $this->action;
    }

    public function isRemoveStrategyAction(): bool
    {
        return self::ACTION_REMOVE_STRATEGY === $this->action;
    }
}
