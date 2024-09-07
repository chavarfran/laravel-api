#!/bin/bash
set -eo pipefail

PROJECT_H="/var/www/${GIT_REPO}"

cd "$PROJECT_H"
yarn install
exec yarn run dev
