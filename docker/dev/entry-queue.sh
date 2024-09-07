#!/bin/bash
set -eo pipefail

PROJECT_H="/var/www/${GIT_REPO}"
cd "$PROJECT_H"
exec php artisan queue:listen --verbose
