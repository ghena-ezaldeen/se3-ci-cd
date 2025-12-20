<?php

namespace App\Patterns\Decorator;

/*
|--------------------------------------------------------------------------
| Account Decorator (Base)
|--------------------------------------------------------------------------
| Base decorator class that wraps an AccountComponent to add features
| dynamically without modifying the underlying account structure. This
| follows the Decorator pattern, allowing us to stack multiple features
| on top of accounts while maintaining compatibility with the Composite
| pattern used for parent/child account hierarchies.
|
| Decorators can modify behavior like available balance, withdrawal limits,
| interest rates, or transaction fees without changing the core account
| model or composite tree structure.
*/

use App\Patterns\Composite\AccountComponent;

abstract class AccountDecorator extends AccountComponent
{
    protected AccountComponent $component;

    public function __construct(AccountComponent $component)
    {
        parent::__construct($component->getAccount());
        $this->component = $component;
    }

    /**
     * Get the base balance from the wrapped component.
     * Subclasses can override this to apply feature-specific modifications.
     */
    public function getBalance(): float
    {
        return $this->component->getBalance();
    }

    /**
     * Get the wrapped component for nested decorator chains.
     */
    public function getComponent(): AccountComponent
    {
        return $this->component;
    }

    /**
     * Get children from the wrapped component to maintain composite compatibility.
     */
    public function getChildren(): array
    {
        return $this->component->getChildren();
    }

    /**
     * Delegate add operation to wrapped component.
     */
    public function add(AccountComponent $component): void
    {
        $this->component->add($component);
    }

    /**
     * Delegate remove operation to wrapped component.
     */
    public function remove(AccountComponent $component): void
    {
        $this->component->remove($component);
    }

    /**
     * Find a decorator of a specific type in the decorator chain.
     * Useful for accessing methods from nested decorators.
     *
     * @param string $decoratorClass The class name of the decorator to find
     * @return AccountDecorator|null The decorator instance if found, null otherwise
     */
    public function findDecorator(string $decoratorClass): ?AccountDecorator
    {
        if ($this instanceof $decoratorClass) {
            return $this;
        }

        if ($this->component instanceof AccountDecorator) {
            return $this->component->findDecorator($decoratorClass);
        }

        return null;
    }
}

