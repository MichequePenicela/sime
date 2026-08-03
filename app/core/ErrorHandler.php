<?php

function safeEcho(callable $callback, string $fallback = 'Erro inesperado') {
    try {
        echo $callback();
    } catch (Throwable $e) {
        error_log($e);
        echo "<span class='text-danger'>{$fallback}</span>";
    }
}
