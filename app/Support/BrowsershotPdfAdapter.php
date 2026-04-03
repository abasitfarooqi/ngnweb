<?php

namespace App\Support;

use Spatie\Browsershot\Browsershot;

class BrowsershotPdfAdapter
{
    private string $view;

    private array $data;

    private string $paper = 'a4';

    private string $orientation = 'portrait';

    private array $options = [];

    private ?string $bytes = null;

    public function __construct(string $view, array $data = [])
    {
        $this->view = $view;
        $this->data = $data;
    }

    public function setPaper(string $paper, string $orientation = 'portrait'): self
    {
        $this->paper = $paper;
        $this->orientation = $orientation;

        return $this;
    }

    public function setOption(string $key, mixed $value): self
    {
        $this->options[$key] = $value;

        return $this;
    }

    public function save(string $path): self
    {
        $bytes = $this->output();
        file_put_contents($path, $bytes);

        return $this;
    }

    public function output(): string
    {
        if ($this->bytes !== null) {
            return $this->bytes;
        }

        $html = view($this->view, $this->data)->render();
        $browser = Browsershot::html($html);

        $paper = strtoupper($this->paper);
        if ($paper === 'A4') {
            $browser->format('A4');
        }
        if (strtolower($this->orientation) === 'landscape') {
            $browser->landscape();
        }

        $this->bytes = $browser->showBackground()->pdf();

        return $this->bytes;
    }
}
