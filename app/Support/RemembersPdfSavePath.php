<?php

namespace App\Support;

final class RemembersPdfSavePath
{
    private ?string $savedPath = null;

    public function __construct(private mixed $inner) {}

    public function setPaper(string $paper, string $orientation = 'portrait'): self
    {
        $this->inner->setPaper($paper, $orientation);

        return $this;
    }

    public function setOption(string $key, mixed $value): self
    {
        $this->inner->setOption($key, $value);

        return $this;
    }

    public function save(string $path): self
    {
        $this->inner->save($path);
        $this->savedPath = $path;

        return $this;
    }

    public function output(): mixed
    {
        return $this->inner->output();
    }

    public function savedPath(): ?string
    {
        return $this->savedPath;
    }

    public function __call(string $method, array $arguments): mixed
    {
        $result = $this->inner->{$method}(...$arguments);

        return $result === $this->inner ? $this : $result;
    }
}
