# SP Global Tournament API

Прокси-сервер для глобального доступа к турнирам SurvivalPoint.

## Архитектура

```
Игроки (мир) --> Render.com (прокси) --> spanalytic.ru (мастер)
Игроки (РФ)  --> spanalytic.ru (напрямую)
```

## Деплой на Render.com

1. Создать новый Web Service
2. Подключить этот репозиторий
3. Render автоматически обнаружит `render.yaml`
4. Нажать Deploy

## Endpoints

Все endpoints идентичны основному серверу:

- `GET /status.php?player_id={id}` - статус игрока
- `GET /leaderboard.php?player_id={id}` - топ-100
- `POST /submit_score.php` - отправить очки
- `POST /claim_rewards.php` - забрать награды
- `GET /get_config.php` - конфигурация турнира

## Переменные окружения

- `RF_SERVER_URL` - URL мастер-сервера (по умолчанию: https://spanalytic.ru/api/tournament)
