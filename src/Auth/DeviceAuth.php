<?php

declare(strict_types=1);

namespace Fbns\Auth;

use Fbns\Auth;
use Fbns\Json;
use Ramsey\Uuid\Uuid;

class DeviceAuth implements Auth, \JsonSerializable
{
    private const TYPE = 'device_auth';

    private const CLIENT_ID_LENGTH = 20;

    /** @var string */
    private $clientId;

    /** @var int */
    private $userId;

    /** @var string */
    private $password;

    /** @var string */
    private $deviceId;

    /** @var string */
    private $deviceSecret;

    private function randomUuid(): string
    {
        return Uuid::uuid4()->toString();
    }

    private function defaultClientId(): string
    {
        return substr($this->randomUuid(), 0, self::CLIENT_ID_LENGTH);
    }

    public function __construct()
    {
        $this->clientId = $this->defaultClientId();
        $this->userId = 0;
        $this->password = '';
        $this->deviceSecret = '';
        $this->deviceId = '';
    }

    public function read(string $json)
    {
        $data = Json::decode($json);

        if (isset($data->ck)) {
            $this->userId = $data->ck;
        }
        if (isset($data->cs)) {
            $this->password = $data->cs;
        }
        if (isset($data->di)) {
            $this->deviceId = $data->di;
            $this->clientId = substr($this->deviceId, 0, self::CLIENT_ID_LENGTH);
            if ($this->clientId === '') {
                $this->clientId = $this->defaultClientId();
            }
        }
        if (isset($data->ds)) {
            $this->deviceSecret = $data->ds;
        }

        // TODO: sr ?
        // TODO: rc ?
    }

    public function jsonSerialize()
    {
        return [
            'ck' => $this->userId,
            'cs' => $this->password,
            'di' => $this->deviceId,
            'ds' => $this->deviceSecret,
        ];
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getDeviceId(): string
    {
        return $this->deviceId;
    }

    public function getDeviceSecret(): string
    {
        return $this->deviceSecret;
    }

    public function getClientType(): string
    {
        return self::TYPE;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }
}
