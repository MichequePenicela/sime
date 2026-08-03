<?php

namespace App\Core;

trait Flash
{
    protected function flash(
        string $titulo,
        string $mensagem,
        string $tipo = 'info',
        string $icon = 'info-circle'
    ): void {
        $_SESSION['flash_modal'] = compact(
            'titulo',
            'mensagem',
            'tipo',
            'icon'
        );
    }

    protected function flashSuccess(string $mensagem, string $titulo = 'Sucesso'): void
    {
        $this->flash($titulo, $mensagem, 'success', 'check-circle');
    }

    protected function flashError(string $mensagem, string $titulo = 'Erro'): void
    {
        $this->flash($titulo, $mensagem, 'danger', 'exclamation-triangle');
    }

    protected function flashWarning(string $mensagem, string $titulo = 'Atenção'): void
    {
        $this->flash($titulo, $mensagem, 'warning', 'exclamation-circle');
    }
}
