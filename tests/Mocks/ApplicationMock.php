<?php

declare(strict_types=1);

namespace Bitrix\Main;

/**
 * Mock for Bitrix\Main\Application.
 * Provides a request context that returns null for all get() calls,
 * so saveFromRequest() will not execute during tests.
 */
class Application
{
    private static ?self $instance = null;
    private ?Context $context = null;

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getContext(): Context
    {
        if ($this->context === null) {
            $this->context = new Context();
        }

        return $this->context;
    }

    /**
     * Resets the singleton instance. Use in test tear-down methods.
     */
    public static function reset(): void
    {
        self::$instance = null;
    }
}

/**
 * Mock request context.
 */
class Context
{
    private ?Request $request = null;

    public function getRequest(): Request
    {
        if ($this->request === null) {
            $this->request = new Request();
        }

        return $this->request;
    }
}

/**
 * Mock HTTP request.
 * All get() calls return null so saveFromRequest() exits early.
 */
class Request
{
    /**
     * @var array<string, mixed>
     */
    private array $params = [];

    /**
     * @param string $key Query parameter name
     * @return mixed Returns null by default; returns stored value if set via setParam()
     */
    public function get(string $key): mixed
    {
        return $this->params[$key] ?? null;
    }

    /**
     * Sets a parameter value for testing.
     * Use this in tests to simulate request data.
     */
    public function setParam(string $key, mixed $value): void
    {
        $this->params[$key] = $value;
    }
}
