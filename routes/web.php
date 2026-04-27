<?php

use Cord\Cord;
use Illuminate\Support\Facades\Route;

foreach (Cord::getPanels() as $panel) {
    Route::middleware($panel->getMiddleware())
        ->prefix($panel->getPath())
        ->name("cord.{$panel->getId()}.")
        ->group(function () use ($panel) {
            foreach ($panel->getDiscoveredClasses() as $class) {
                $class::registerRoutes($panel->getPath());
            }
        });
}
