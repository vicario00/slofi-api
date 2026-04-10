#!/bin/bash
# Wrapper para artisan que carga pdo_sqlite
export PHP_INI_SCAN_DIR="/home/erickesc/Documents/GitHub/nameless-finance-app/.phpini"
exec php "$@"
