<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Agendamento (Scheduler)
|--------------------------------------------------------------------------
| Na Hostinger compartilhada cadastre UM único cron job no hPanel:
|
|   * * * * * cd /home/USUARIO/dominio && php artisan schedule:run >> /dev/null 2>&1
|
| Todo o resto (suspensões, encerramento, recorrências e a fila) é disparado
| a partir daqui.
*/

// Atualiza status de suspensão/reativação automática (diário).
Schedule::command('atletas:sincronizar-status')->dailyAt('00:05');

// Encerra peladas vencidas e processa as faltas (a cada hora).
Schedule::command('peladas:processar-encerradas')->hourly();

// Gera as próximas peladas a partir das recorrências (diário).
Schedule::command('peladas:gerar-recorrentes')->dailyAt('00:10');

// Processa a fila (conversões de imagem etc.) sem worker dedicado:
// um único cron já mantém a fila andando a cada minuto.
Schedule::command('queue:work --stop-when-empty --max-time=50 --tries=3')
    ->everyMinute()
    ->withoutOverlapping();
