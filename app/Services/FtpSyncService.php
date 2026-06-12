<?php

namespace App\Services;

class FtpSyncService
{
    /**
     * Remote document mirroring is disabled in this deployment.
     * Keep the method so existing callers do not break, but do nothing.
     */
    public function uploadFile(string $localFullPath): bool
    {
        return false;
    }
}
