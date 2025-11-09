<?php
namespace App\Core;

abstract class Controller {
    protected function view(string $path, array $data = []) {
        extract($data, EXTR_SKIP);
        include __DIR__ . '/../Views/' . $path;
    }
}
