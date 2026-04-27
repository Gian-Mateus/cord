<?php

namespace Cord\Contracts;

interface Registrable
{
    public static function registerRoutes(string $panelPath): void;

    public static function getSlug(): string;

    public static function getRouteName(): string;
}
