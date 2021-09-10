<?php

namespace Source\Modules;

interface ModuleInterface
{
    public function httpGet(array $payload, bool $api = true);
    public function httpPost(array $payload);
    public function httpPut(int $identity, array $payload);
    public function httpDel($identity, array $payload);
    public function httpFileUpload(int $identity, array $payload);
}
