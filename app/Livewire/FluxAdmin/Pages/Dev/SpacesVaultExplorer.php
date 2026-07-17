<?php

namespace App\Livewire\FluxAdmin\Pages\Dev;

use App\Support\SpacesVault;
use App\Support\SpacesVaultAccess;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Spaces vault')]
class SpacesVaultExplorer extends Component
{
    #[Url(as: 'p', except: '')]
    public string $path = '';

    #[Url(as: 'q', except: '')]
    public string $filter = '';

    public ?string $previewPath = null;

    public function mount(): void
    {
        SpacesVaultAccess::authorize();

        try {
            $this->path = SpacesVault::normalizePath($this->path);
        } catch (\InvalidArgumentException) {
            $this->path = '';
        }
    }

    public function enterFolder(string $name): void
    {
        $name = trim(str_replace('\\', '/', $name), '/');

        if ($name === '' || str_contains($name, '/')) {
            return;
        }

        $this->path = $this->path === '' ? $name : rtrim($this->path, '/').'/'.$name;
        $this->previewPath = null;
    }

    public function goUp(): void
    {
        if ($this->path === '') {
            return;
        }

        $parent = dirname($this->path);

        $this->path = $parent === '.' ? '' : $parent;
        $this->previewPath = null;
    }

    public function goTo(string $path): void
    {
        try {
            $this->path = SpacesVault::normalizePath($path);
            $this->previewPath = null;
        } catch (\InvalidArgumentException) {
            $this->path = '';
            $this->previewPath = null;
        }
    }

    public function openPreview(string $path): void
    {
        try {
            $this->previewPath = SpacesVault::normalizePath($path);
        } catch (\InvalidArgumentException) {
            $this->previewPath = null;
        }
    }

    public function closePreview(): void
    {
        $this->previewPath = null;
    }

    public function render()
    {
        $configured = SpacesVault::configured();
        $listing = ['path' => '', 'breadcrumbs' => [], 'folders' => [], 'files' => []];
        $error = null;

        if ($configured) {
            try {
                $listing = SpacesVault::listing($this->path, $this->filter !== '' ? $this->filter : null);
            } catch (\Throwable $e) {
                $error = 'Could not read bucket: '.$e->getMessage();
            }
        }

        $previewFile = null;

        if ($this->previewPath && $configured) {
            foreach ($listing['files'] as $file) {
                if ($file['path'] === $this->previewPath) {
                    $previewFile = $file;
                    break;
                }
            }

            if ($previewFile === null && SpacesVault::disk()->exists($this->previewPath)) {
                $mime = null;
                try {
                    $mime = SpacesVault::disk()->mimeType($this->previewPath);
                } catch (\Throwable) {
                }

                $previewFile = [
                    'name' => basename($this->previewPath),
                    'path' => $this->previewPath,
                    'mime' => $mime,
                    'previewable' => SpacesVault::isPreviewable($mime, basename($this->previewPath)),
                ];
            }
        }

        return view('flux-admin.pages.dev.spaces-vault', [
            'configured' => $configured,
            'listing' => $listing,
            'error' => $error,
            'previewFile' => $previewFile,
            'bucket' => config('filesystems.disks.'.SpacesVault::diskName().'.bucket'),
        ]);
    }
}
