<?php

declare(strict_types=1);

namespace App\Json;

class JsonPatch {
    public function merge(array &$payload, array &$changes): void
    {
        foreach ($changes as $key => $value) {
            if (true === $this->isObject($value)) {
                if (false === array_key_exists($key, $payload) || false === $this->isObject($payload[$key])) {
                    $payload[$key] = [];
                }

                $this->merge($payload[$key], $value);

                continue;
            }            

            if (null === $value) {
                unset($payload[$key]);

                continue;
            }

            $payload[$key] = $value;
        }
    }

    public function isObject(mixed $value): bool
    {
        if (false === is_array($value)) {
            return false;
        }

        if ([] === $value) {
            return true;
        }

        return array_keys($value) !== range(0, count($value) - 1);
    }
}