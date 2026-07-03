#!/bin/zsh
# WP-CLI wrapper for the northeastern-research Local site.
# Usage: ./wp.sh <wp-cli args...>
PHPBIN="$HOME/Library/Application Support/Local/lightning-services/php-8.2.29+0/bin/darwin-arm64/bin/php"
SOCK="$HOME/Library/Application Support/Local/run/lXEkfeBsU/mysql/mysqld.sock"
PHAR="/Applications/Local.app/Contents/Resources/extraResources/bin/wp-cli/wp-cli.phar"
SITE="/Users/darshanaswathappa/Local Sites/northeastern-research/app/public"
exec "$PHPBIN" -d mysqli.default_socket="$SOCK" "$PHAR" --path="$SITE" "$@"
